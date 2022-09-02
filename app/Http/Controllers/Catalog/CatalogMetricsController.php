<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CatalogMetricsController extends Controller
{
    public function Metrics()
    {
        $sources = ['in', 'us'];
        $total_asin = 0;
        $total_catalog = 0 ;
        foreach($sources as $source){
            $catalog_table = "catalognew${source}s";
            $table_name = table_model_create(country_code:$source, model:'Asin_source', table_name:'asin_source_');
            $total_asin += $table_name->get('asin')->count();
            $total_catalog += DB::connection('catalog')->table($catalog_table )->count('asin');
        }
        
       return view('Catalog.Metrics.index', compact('total_asin', 'total_catalog'));
    }
}
