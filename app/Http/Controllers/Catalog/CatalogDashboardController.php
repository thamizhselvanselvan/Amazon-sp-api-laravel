<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CatalogDashboardController extends Controller
{
    public function Metrics()
    {
        $sources = ['IN', 'US'];
        $total_asin = 0;
        $total_catalog = 0 ;
        $priority_wise = [];
        $Total_catalog = [];
        $record_arrays = [];
        $delist_asin = [];
        $cat_price = [];
        $dbname = config('database.connections.buybox.database');
        foreach($sources as $source){
            $table_name = table_model_create(country_code:$source, model:'Asin_destination', table_name:'asin_destination_');
            $source = strtolower($source);
            $destination_table = "asin_destination_${source}s";
            $catalog_table = "catalognew${source}s";
            $total_asin += $table_name->get('asin')->count();
            $total_catalog += DB::connection('catalog')->table($catalog_table )->count('asin');

            $buybox_table = "bb_product_${source}s_lp_offers";
            $catalog_price = "pricing_${source}s";

            for($priority=1; $priority<=3; $priority++){

                $priority_wise []= $table_name->where('priority', $priority)->get('asin')->count();
                $Total_catalog []= $table_name->where($destination_table.'.priority', $priority)
                ->join($catalog_table, $destination_table.'.asin', '=', $catalog_table.'.asin')->count();

                $delist_asin []= $table_name->where($destination_table.'.priority', $priority)
                ->where($buybox_table.'.delist', 1)
                ->join($dbname.".${buybox_table}", $destination_table.'.asin', '=', $buybox_table.'.asin')->count();
                
                $cat_price []= $table_name->where($destination_table.'.priority', $priority)
                ->join($catalog_price, $destination_table.'.asin', '=', $catalog_price.'.asin')->count();
                

            }
            
            $record_arrays [] = [
                'source' => $source,
                'priority_wise_asin' => $priority_wise,
                'catalog' => $Total_catalog,
                'delist_asin' => $delist_asin,
                'catalog_price' => $cat_price,
            ];
            $priority_wise = [];
            $Total_catalog = [];
            $delist_asin = [];
            $cat_price = [];
        }
        // po($record_arrays);
       return view('Catalog.Dashboard.index', compact('total_asin', 'total_catalog', 'record_arrays'));
    }
}
