<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CatalogMetricsController extends Controller
{
    public function Metrics()
    {
        $sources = ['IN', 'US'];
        $total_asin = 0;
        $total_catalog = 0 ;
        $priority_wise = [];
        $Total_catalog = [];
        foreach($sources as $source){
            $table_name = table_model_create(country_code:$source, model:'Asin_destination', table_name:'asin_destination_');
            $source = strtolower($source);
            $destination_table = "asin_destination_${source}s";
            $catalog_table = "catalognew${source}s";
            $total_asin += $table_name->get('asin')->count();
            $total_catalog += DB::connection('catalog')->table($catalog_table )->count('asin');
            for($priority=1; $priority<=3; $priority++){
                $priority_wise [][$source]= $table_name->where('priority', $priority)->get('asin')->count();
                $Total_catalog [][$source]= $table_name->where($destination_table.'.priority', $priority)
                ->join($catalog_table, $destination_table.'.asin', '=', $catalog_table.'.asin')->count();
            }
            
            // po($Total_catalog);
            
        }
       return view('Catalog.Metrics.index', compact('total_asin', 'total_catalog', 'priority_wise', 'Total_catalog'));
    }
}
