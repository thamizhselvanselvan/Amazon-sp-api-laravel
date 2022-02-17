<?php

namespace App\Http\Controllers;

// use RedBeanPHP\R;
use League\Csv\Reader;
use \RedBeanPHP\R as R;
use League\Csv\Statement;
use Illuminate\Http\Request;
use League\Csv\XMLConverter;
use App\Models\universalTextile;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Jobs\universalTextileDataImport;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Log;

class textilesController extends Controller
{   
    
    public function index(Request $request)
    {  
        
        if($request->ajax()){

             // $getData = ['id','textile', 'ean', 'brand','title','size','color','transfer_price','shipping_weight','product_type','quantity'];
            $data = universalTextile::query();
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('textiles_id', function($row){
                    return ($row->textile);
                })
                ->rawColumns(['textiles_id'])
                ->make(true);
        }
        return view('textiles.index');
    }

    public function importTextiles()
    {
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
            
            // exec('nohup php artisan pms:textiles-import  > /dev/null &');
            exec('php artisan pms:textiles-import  > /dev/null &');
            
            Log::warning("Script executed production  !!!");
        } else {

            Log::warning("Script executed local !");
            Artisan::call('pms:textiles-import');
        }

        return view('textiles.index');
    }

}
