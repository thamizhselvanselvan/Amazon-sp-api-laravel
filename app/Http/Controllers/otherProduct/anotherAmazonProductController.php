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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class anotherAmazonProductController extends Controller
{
    private $offset = 0;
    public function index(Request $request)
    {
        if($request->ajax()){
            $data = OthercatDetails::query()->limit(1);

            return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('availability', function ($row) {
                return $row ? 'Available' : 'NA';
            })
            ->editColumn('hs_code_gst', function ($row) {
                return $row->hs_code ." / ". $row->gst ;
            })
            ->editColumn('price1_price_inr', function ($row) {
                return $row->price1 ." / ". $row->price_inr ;
            })

            ->editColumn('flipkart_amazon', function ($row) {
                return $row->flipkart ." / ". $row->amazon;
            })

            ->editColumn('uae_latency_b2c_latency', function ($row) {
                return $row->uae_latency ." / ". $row->b2c_latency;
            })

            ->editColumn('image_p_image_d', function ($row) {
                return $row->image_p ." / ". $row->image_d;
            })
            
            ->editColumn('height_length_width', function ($row) {
                return $row->height ." / ". $row->length ." / ". $row->width;
            })
            ->editColumn('model_mpn', function ($row) {
                return $row->model ." / ". $row->mpn;
            })

            ->rawcolumns(['availability','image_p_image_d','hs_code_gst','price1_price_inr','uae_latency_b2c_latency','flipkart_amazon','height_length_width','model_mpn'])          
            ->make(true);

        }
        return view('amazonOtherProduct.index');
    }

    public function exportOtherProduct(Request $request)
    {
        $selected_header = $request->input('selected');
        
            $selected_header=  $selected_header;
        
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
            
            // exec('nohup php artisan pms:textiles-import  > /dev/null &');
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:export-other-amazon $selected_header > /dev/null &";
            exec($command);
            
            Log::warning("Export asin command executed production  !!!");
        } else {

            Log::warning("Export asin command executed local !");
            Artisan::call('pms:export-other-amazon '.$selected_header);
        }
        
    }

    
}
