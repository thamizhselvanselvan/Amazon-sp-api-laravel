<?php

namespace App\Services\SP_API\API;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CatalogDashboardService
{
    public $gross;
    public $bb_delist_count = [];
    public function catalogDashboard()
    {
        $sources = ['IN', 'US'];
        $record_arrays = [];
        $dbname = config('database.connections.catalog.database');
        foreach ($sources as $source) {

            $asin_priority = [1 => 0, 2 => 0, 3 => 0];
            $catalog = [1 => 0, 2 => 0, 3 => 0];
            $bb_asin_delist = [1 => 0, 2 => 0, 3 => 0];
            // $asin_bb_price = [1 => 0, 2 => 0, 3 => 0];
            $cat_price = [1 => 0, 2 => 0, 3 => 0];

            $delist_asin_count = [];
            $source = strtolower($source);
            $destination_table = "asin_destination_${source}s";
            $catalog_table = "catalognew${source}s";
            $catalog_price = "pricing_${source}s";

            $priority_wise = DB::connection('catalog')
                ->select("SELECT count(asin) as priority_wise, priority from ${destination_table} 
            group by priority");
            foreach ($priority_wise as $priority) {
                $value = $priority->priority;
                $asin_priority[$value] = $priority->priority_wise;
            }

            $Total_catalogs = DB::connection('catalog')
                ->select("SELECT count(${destination_table}.asin) as asin_catalog ,${destination_table}.priority from ${destination_table}
            join ${catalog_table}
            ON ${destination_table}.asin = ${catalog_table}.asin
            group by ${destination_table}.priority
            ");

            foreach ($Total_catalogs as $total_catalog) {
                $cat = $total_catalog->priority;
                $catalog[$cat] = $total_catalog->asin_catalog;
            }

            $this->bb_delist_count = [];
            $table = table_model_create(country_code: $source, model: 'asin_destination', table_name: 'asin_destination_');
            for ($priority = 1; $priority <= 3; $priority++) {

                $this->gross = 0;
                $data = $table->select('id', 'asin')->where('priority', $priority)->chunkbyid(5000, function ($result) use ($priority, $source) {
                    foreach ($result as $delist_asin) {
                        $asins[] = "'$delist_asin->asin'";
                    }
                    $asin = implode(',', $asins);
                    $buybox_table = "bb_product_aa_custom_p${priority}_${source}_offers";
                    $delist_asin_count[] = DB::connection('buybox')->select("SELECT count(asin)as asin_delist
                        FROM ${buybox_table} 
                        WHERE asin IN ($asin)
                        and delist = 1
                        ");

                    foreach ($delist_asin_count as $asin_delist) {
                        if (isset($asin_delist[0])) {
                            $this->gross = $this->gross + $asin_delist[0]->asin_delist;
                        }
                    }
                });
                $this->bb_delist_count[] = [
                    'asin_delist'   => $this->gross,
                    'priority'  => $priority,
                ];
            }
            log::alert($this->bb_delist_count);

            foreach ($this->bb_delist_count as $delist_asin) {
                $delist = $delist_asin['priority'];
                $bb_asin_delist[$delist] = $delist_asin['asin_delist'];
            }


            $cat_pricings = DB::connection('catalog')
                ->select("SELECT count(${destination_table}.asin) as price, ${destination_table}.priority from ${destination_table}
            join ${catalog_price}
            ON ${destination_table}.asin = ${catalog_price}.asin
            group by ${destination_table}.priority
            ");
            foreach ($cat_pricings as $cat_pricing) {
                $pr_priority = $cat_pricing->priority;
                $cat_price[$pr_priority] = $cat_pricing->price;
            }

            $record_arrays[]  = [
                'priority_wise_asin' => $asin_priority,
                'catalog' => $catalog,
                'delist_asin' => $bb_asin_delist,
                'catalog_price' => $cat_price,
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
