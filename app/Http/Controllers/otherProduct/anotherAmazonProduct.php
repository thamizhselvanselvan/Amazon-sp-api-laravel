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
        Log::warning("warning form exprot ");   
        $file_path = "excel/downloads/otheramazon/otherProductDetails";
        $chunk = 10000;

     $header = ['hit', 'asin', 'sku', 'hs_code', 'gst', 'update_time', 'availability', 'price','list_price','price1','price_inr','list_price_inr','price_aed','list_price_aed', 'shipping_weight', 'image_t', 'id', 'title','image_p', 'image_d', 'category', 'all_category', 'description', 'height' ,'length' ,'width' ,'weight' ,'flipkart', 'amazon', 'upc', 'manufacturer	' ,'latency' ,'uae_latency' ,'b2c_latency' ,'ean' ,'color' ,'model' ,'mpn' ,'detail_page_url' ,'creation_time', 'page'];
     
    OthercatDetails::chunk($chunk, function ($records) use($file_path, $header) {

        if(!Storage::exists($file_path.$this->offset.'.csv')) {
            Storage::put($file_path.$this->offset.'.csv', '');}

        $writer = Writer::createFromPath(Storage::path($file_path.$this->offset.'.csv'), "w");

        $writer->insertOne($header);
        $records = $records->toArray();
        $records = array_map(function ($datas) {
            return (array) $datas;
        }, $records);
        
        $writer->insertall($records);  
        $this->offset++;

    });
        
    }

    
}
