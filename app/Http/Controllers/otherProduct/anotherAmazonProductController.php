<?php

namespace App\Http\Controllers\otherProduct;

use League\Csv\Writer;
use Illuminate\Http\Request;
use App\Models\OthercatDetails;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class anotherAmazonProductController extends Controller
{
    private $offset = 0;
    public function index(Request $request)
    {
        if($request->ajax()){
            $data = OthercatDetails::query();
            return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('availability', function ($row) {
                return $row ? 'Available' : 'NA';
            })
            ->make(true);

        }
        return view('amazonOtherProduct.index');
    }

    public function exportOtherProduct()
    {
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
            
            // exec('nohup php artisan pms:textiles-import  > /dev/null &');
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:export-other-amazon > /dev/null &";
            exec($command);
            
            Log::warning("Export asin command executed production  !!!");
        } else {

            Log::warning("Export asin command executed local !");
            Artisan::call('pms:export-other-amazon');
        }
        
    }

    
}
