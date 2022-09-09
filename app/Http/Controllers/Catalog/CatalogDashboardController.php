<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CatalogDashboardController extends Controller
{
    public function Metrics()
    {
        $sources = ['IN', 'US'];
        $record_arrays = [];
        $dbname = config('database.connections.buybox.database');
        foreach($sources as $source){
            
            $asin_priority = [1 => 0,2 => 0,3 => 0];
            $catalog = [1 => 0, 2 => 0, 3 => 0];
            $asin_delist = [1 => 0,2 => 0,3 => 0];
            $asin_catalog_price = [1 => 0,2 => 0,3 => 0];
            $source = strtolower($source);
            $destination_table = "asin_destination_${source}s";
            $catalog_table = "catalognew${source}s";
            $buybox_table = "bb_product_${source}s_lp_offers";
            $catalog_price = "pricing_${source}s";

            $priority_wise = DB::connection('catalog')
            ->select("SELECT count(asin) as priority_wise, priority from ${destination_table} 
            group by priority");
            foreach($priority_wise as $priority)
            {
                $value = $priority->priority;
                $asin_priority[$value] = $priority->priority_wise;
            }
            
            $Total_catalogs = DB::connection('catalog')
            ->select("SELECT count(${destination_table}.asin) as asin_catalog ,${destination_table}.priority from ${destination_table}
            join ${catalog_table}
            where ${destination_table}.asin = ${catalog_table}.asin
            group by ${destination_table}.priority
            ");
            
            foreach($Total_catalogs as $total_catalog)
            {
                $cat = $total_catalog->priority;
                $catalog[$cat] = $total_catalog->asin_catalog;
            }
            po($catalog);

            $delist_asins = DB::connection('catalog')
            ->select("SELECT count(${destination_table}.asin) as asin_delist, ${destination_table}.priority from ${destination_table}
            join ${dbname}.${buybox_table}
            where $destination_table.asin = $buybox_table.asin
            and ${buybox_table}.delist = 1
            group by ${destination_table}.priority
            ");

            foreach($delist_asins as $delist_asin){
                $delist = $delist_asin->priority;
                $asin_delist[$delist] = $delist_asin->asin_delist;
            }
            
            $cat_prices =  DB::connection('catalog')
            ->select("SELECT count(${destination_table}.asin) as catalog_price, ${destination_table}.priority from ${destination_table}
            join ${catalog_price}
            where ${destination_table}.asin = ${catalog_price}.asin
            group by ${destination_table}.priority
            ");
            foreach($cat_prices as $cat_price){
                $price = $cat_price->priority;
                $asin_catalog_price[$price] = $cat_price->catalog_price;
            }
                
            $record_arrays []  = [
            'priority_wise_asin' => $asin_priority,
            'catalog' => $catalog,
            'delist_asin' => $asin_delist,
            'catalog_price' => $asin_catalog_price,
            ];
            // $asin_priority = [];
            // $asin_catalog = [];
            // $asin_delist = [];
            // $asin_catalog_price = [];
        }
        // exit;
        $cat_dashboard_file = "excel/downloads/catalog-dashboard-file.json";
        // $cat_dashboard_file = "Dashboard/catalog-dashboard-file.json";
        if(!Storage::exists($cat_dashboard_file)){
            Storage::put($cat_dashboard_file, '');
        }
        Storage::put($cat_dashboard_file, json_encode($record_arrays));
        $cat_dashboard_file = "excel/downloads/catalog-dashboard-file.json";
        $json_arrays = json_decode(Storage::get($cat_dashboard_file));

        
        // po($json_arrays);
       return view('Catalog.Dashboard.index', compact('json_arrays'));
    }
}
