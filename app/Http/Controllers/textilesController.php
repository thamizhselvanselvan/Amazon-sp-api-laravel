<?php

namespace App\Http\Controllers;

// use RedBeanPHP\R;
use PDO;
use ArrayIterator;
use League\Csv\Reader;
use League\Csv\Writer;
use SplTempFileObject;
use League\Csv\Statement;
use Illuminate\Http\Request;
use App\Models\universalTextile;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Cache\RateLimiting\Limit;
use Psy\CodeCleaner\FunctionReturnInWriteContextPass;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;


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
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:textiles-import > /dev/null &";
            exec($command);
            
            Log::warning("Script executed production  !!!");
        } else {

            Log::warning("Script executed local !");
            Artisan::call('pms:textiles-import');
        }

        return view('textiles.index');
    }

    public function exportTextilesToCSV()
    {
      
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
            
            // exec('nohup php artisan pms:textiles-import  > /dev/null &');
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:textiles-export > /dev/null &";
            exec($command);
            
            Log::warning("Export command executed production  !!!");
        } else {

            Log::warning("Export coma executed local !");
            Artisan::call('pms:textiles-export');
        }

        return redirect()->intended('/textiles');

    }
    
    public function download_universalTextiles()
    {

        $file_path = "excel/downloads/universalTextilesExport.csv";
        //$path = Storage::path($file_path);
        if (Storage::exists($file_path)) {
            return Storage::download($file_path);
        }
        return 'file not exist';
    }

}
