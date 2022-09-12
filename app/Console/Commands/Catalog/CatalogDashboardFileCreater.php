<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CatalogDashboardFileCreater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog-dashboard-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create json file for catalog dashboard';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sources = ['IN', 'US'];
        $record_arrays = [];
        $dbname = config('database.connections.buybox.database');
        foreach ($sources as $source) {

            $asin_priority = [1 => 0, 2 => 0, 3 => 0];
            $catalog = [1 => 0, 2 => 0, 3 => 0];
            $asin_delist = [1 => 0, 2 => 0, 3 => 0];
            $asin_bb_price = [1 => 0, 2 => 0, 3 => 0];
            $cat_price = [1 => 0, 2 => 0, 3 => 0];

            $source = strtolower($source);
            $destination_table = "asin_destination_${source}s";
            $catalog_table = "catalognew${source}s";
            $buybox_table = "bb_product_${source}s_lp_offers";
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

            $delist_asins = DB::connection('catalog')
                ->select("SELECT count(${destination_table}.asin) as asin_delist, ${destination_table}.priority from ${destination_table}
            join ${dbname}.${buybox_table}
            ON ${destination_table}.asin = ${buybox_table}.asin
            WHERE ${buybox_table}.delist = 1
            group by ${destination_table}.priority
            ");

            foreach ($delist_asins as $delist_asin) {
                $delist = $delist_asin->priority;
                $asin_delist[$delist] = $delist_asin->asin_delist;
            }

            $bb_prices =  DB::connection('catalog')
                ->select("SELECT count(${destination_table}.asin) as catalog_price, ${destination_table}.priority from ${destination_table}
            join ${dbname}.${buybox_table}
            ON ${destination_table}.asin = ${buybox_table}.asin
            group by ${destination_table}.priority
            ");
            foreach ($bb_prices as $bb_price) {
                $price = $bb_price->priority;
                $asin_bb_price[$price] = $bb_price->catalog_price;
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
                'delist_asin' => $asin_delist,
                'bb_price' => $asin_bb_price,
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
