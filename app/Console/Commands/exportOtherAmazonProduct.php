<?php

namespace App\Console\Commands;

use League\Csv\Writer;
use App\Models\OthercatDetails;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class exportOtherAmazonProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:export-other-amazon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::warning("warning form exprot ");   
        $file_path = "excel/downloads/otheramazon/otherProductDetails";
        $chunk = 950000;

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
