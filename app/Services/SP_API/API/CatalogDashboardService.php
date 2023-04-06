<?php

namespace App\Services\SP_API\API;

use App\Models\ProcessManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CatalogDashboardService
{
    private $gross;
    private $unavailable;
    private $bb_delist_count = [];
    private $bb_unavailable_count = [];

    public function catalogDashboard()
    {
        $sources = ['IN', 'US'];
        $record_arrays = [];
        $dbname = config('database.connections.catalog.database');
        foreach ($sources as $source) {

            $asin_priority = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $catalog = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $bb_asin_delist = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $cat_price = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $asin_bb_unavailable = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $na_catalog = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
            $cread_array = [1 => 0, 2 => 0, 3 => 0, 4 => 0];

            $delist_asin_count = [];
            $source = strtolower($source);
            $destination_table = "asin_destination_${source}s";
            $catalog_table = "catalog${source}s";
            $catalog_price = "pricing_${source}s";

            $Total_catalogs = DB::connection('catalog')
                ->select("SELECT count(${destination_table}.asin) as asin_catalog,
                ${destination_table}.priority from ${destination_table}
                    join ${catalog_table}
                    ON ${destination_table}.asin = ${catalog_table}.asin
                    group by ${destination_table}.priority
            ");

            foreach ($Total_catalogs as $total_catalog) {
                $cat = $total_catalog->priority;
                $catalog[$cat] = trimTrailingZeroes(formatInIndianStyle($total_catalog->asin_catalog));
            }

            $priority_wise = DB::connection('catalog')
                ->select("SELECT count(asin) as priority_wise, priority from ${destination_table} 
            group by priority");

            foreach ($priority_wise as $priority) {
                $value = $priority->priority;
                $asin_priority[$value] = trimTrailingZeroes(formatInIndianStyle($priority->priority_wise));

                // unavailable catalog start
                $na_catalog[$value] = trimTrailingZeroes(formatInIndianStyle($priority->priority_wise - filter_var($catalog[$value], FILTER_SANITIZE_NUMBER_INT)));
            }

            $this->bb_delist_count = [];
            $this->bb_unavailable_count = [];
            $table = table_model_create(country_code: $source, model: 'Asin_destination', table_name: 'asin_destination_');
            for ($priority = 1; $priority <= 4; $priority++) {

                $this->gross = 0;
                $this->unavailable = 0;
                $data = $table->select('id', 'asin')
                    ->where('priority', $priority)
                    ->chunkbyid(5000, function ($result) use ($priority, $source) {
                        foreach ($result as $delist_asin) {
                            $asins[] = "'$delist_asin->asin'";
                        }
                        $asin = implode(',', $asins);
                        $buybox_table = "bb_product_aa_custom_p${priority}_${source}_offers";
                        $delist_asin_count[] = DB::connection('buybox')
                            ->select("SELECT count(asin)as asin_delist
                            FROM ${buybox_table} 
                            WHERE asin IN ($asin)
                            and delist = 1
                        ");

                        foreach ($delist_asin_count as $asin_delist) {
                            if (isset($asin_delist[0])) {
                                $this->gross = $this->gross + $asin_delist[0]->asin_delist;
                            }
                        }

                        $this->bb_delist_count[] = [
                            'asin_delist'   => $this->gross,
                            'priority'  => $priority,
                        ];

                        //buybox asin unavailable start
                        $bb_unavailable_asins[] = DB::connection('buybox')
                            ->select("SELECT count(asin)as asin_unavailable
                    FROM ${buybox_table}
                    WHERE asin IN ($asin)
                    AND delist = 0
                    AND available != 1
                    ");

                        foreach ($bb_unavailable_asins as $bb_unavailable_asin) {
                            if (isset($bb_unavailable_asin[0])) {
                                $this->unavailable = $this->unavailable + $bb_unavailable_asin[0]->asin_unavailable;
                            }
                        }

                        $this->bb_unavailable_count[] = [
                            'asin_unavailable'  =>  $this->unavailable,
                            'priority'  => $priority,
                        ];

                        //buybox asin unavailable end

                    });
            }

            foreach ($this->bb_delist_count as $delist_asin) {
                $delist = $delist_asin['priority'];
                $bb_asin_delist[$delist] = trimTrailingZeroes(formatInIndianStyle($delist_asin['asin_delist']));
            }

            foreach ($this->bb_unavailable_count as $asin_unavailabe) {
                $unavail_asin = $asin_unavailabe['priority'];
                $asin_bb_unavailable[$unavail_asin] = trimTrailingZeroes(formatInIndianStyle($asin_unavailabe['asin_unavailable']));
            }

            // log::alert($asin_bb_unavailable);

            $cat_pricings = DB::connection('catalog')
                ->select("SELECT count({$destination_table}.asin) as price, 
                {$destination_table}.priority from {$destination_table}
            join ${catalog_price}
            ON ${destination_table}.asin = ${catalog_price}.asin
            where ${catalog_price}.available = 1
            group by ${destination_table}.priority
            ");
            foreach ($cat_pricings as $cat_pricing) {
                $pr_priority = $cat_pricing->priority;
                $cat_price[$pr_priority] = trimTrailingZeroes(formatInIndianStyle($cat_pricing->price));
            }

            start:
            if (Cache::has('creds_count')) {
             
                $creads = Cache::get('creds_count');
                foreach ($creads[$source] as $key => $cread) {
                    $cread_array[$key + 1] = $cread;
                }
            } else {
              
                commandExecFunc('mosh:buybox_priority_count');
                goto start;
            }

            $record_arrays[]  = [
                'priority_wise_asin' => $asin_priority,
                'catalog' => $catalog,
                'delist_asin' => $bb_asin_delist,
                'catalog_price' => $cat_price,
                'asin_unavailable'  =>  $asin_bb_unavailable,
                'na_catalog'    =>  $na_catalog,
                'creds_count' => $cread_array
            ];
        }

        // $cat_dashboard_file = "excel/downloads/catalog-dashboard-file.json";
        $cat_dashboard_file = "Dashboard/catalog-dashboard-file.json";
        if (!Storage::exists($cat_dashboard_file)) {
            Storage::put($cat_dashboard_file, '');
        }
        Storage::put($cat_dashboard_file, json_encode($record_arrays));
    }
}
