<?php

namespace App\Http\Controllers\otherProduct;

use League\Csv\Writer;
use Illuminate\Http\Request;
use App\Models\OthercatDetails;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class anotherAmazonProduct extends Controller
{
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
        Log::warning("warning form exprot ");   
        $file_path = "excel/downloads/otheramazon/otherProductDetails";
        // $file_name = ".csv";
        $offset = 0;
        $chunk = 10000;
    //     if(!Storage::exists($file_path.'otherCatDetails'.$offset.'.csv')) {
    //      Storage::put($file_path.'otherCatDetails'.$offset.'.csv', '');
    //  }
     
    //  $writer = Writer::createFromPath(Storage::path($file_path), "w"); 
     
     $header = ['hit', 'asin', 'sku', 'hs_code', 'gst', 'update_time', 'availability', 'price','list_price','price1','price_inr','list_price_inr','price_aed','list_price_aed', 'shipping_weight', 'image_t', 'id', 'title','image_p', 'image_d', 'category', 'all_category', 'description', 'height' ,'length' ,'width' ,'weight' ,'flipkart', 'amazon', 'upc', 'manufacturer	' ,'latency' ,'uae_latency' ,'b2c_latency' ,'ean' ,'color' ,'model' ,'mpn' ,'detail_page_url' ,'creation_time', 'page'];
     
    //  $header = ['S/N','Asin', 'Source', 'Destination 1', 'Destination 2', 'Destination 3', 'Destination 4', 'Destination 5', 'Created At','Updated At'];

    //  $writer->insertOne($header);
     
    OthercatDetails::chunk($chunk, function ($records) use($file_path, $header, $offset) {

        if(!Storage::exists($file_path.$offset.'.csv')) {
            Storage::put($file_path.$offset.'.csv', '');}

        $writer = Writer::createFromPath(Storage::path($file_path.$offset.'.csv'), "w");

        $writer->insertOne($header);

        $records = $records->toArray();
        // dd($records);
        foreach($records as $record)
        {

            $writer->insertOne($record);
        }
        $offset++;

    });


    //  DB::table('asin_masters')->orderBy('id')->chunk(1000, function ($records) use( $writer) {
         
    //      $records = $records->toArray();
    //      $records = array_map(function ($datas) {
    //          return (array) $datas;
    //      }, $records);
         
    //         $writer->insertall($records);
                    
    //      });
        
    }
}
