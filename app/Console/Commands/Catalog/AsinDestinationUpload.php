<?php

namespace App\Console\Commands\Catalog;

use League\Csv\Reader;
use App\Services\BB\PushAsin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Catalog\AsinDestination;
use Illuminate\Support\Facades\Storage;

class AsinDestinationUpload extends Command
{
    private $destination;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:Asin-destination-upload {user_id} {priority} {--destination=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asin Destination File Upload';

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
        $push_to_bb = new PushAsin();
        $user_id = $this->argument('user_id');
        $priority = $this->argument('priority');
        $destinations = explode(',', $this->option('destination'));
       
        $path = 'AsinDestination/asin.csv';
        $asins = Reader::createFromPath(Storage::path($path), 'r');
        $asins->setHeaderOffset(0);

        $source = buyboxCountrycode();
        $Asin_record = [];
        $product = [];
        $product_lowest_price = [];
        $count = 0;
        foreach($destinations as $this->destination)
        {
            foreach ($asins as  $asin_details) {
                $asin = $asin_details['ASIN'];
                
                $Asin_record[] = [
                    'asin'  => $asin,
                    'user_id'   => $user_id,
                    'priority' => $priority,
                ];
    
                $product[] = [
                    'seller_id' => $source[$this->destination],
                    'active' => 1,
                    'asin1' => $asin,
                ];
    
                $product_lowest_price[] = [
                    'asin' => $asin,
                    'import_type' => 'Seller',
                    'priority'  => $priority,
                ];
    
                if ($count == 999) {
                    
                    $table_name = table_model_create(country_code:$this->destination, model:'Asin_destination', table_name:'asin_destination_');
                    $table_name->upsert($Asin_record, ['user_asin_unique'], ['asin', 'priority']);
                    $push_to_bb->PushAsinToBBTable(product: $product, product_lowest_price: $product_lowest_price, country_code: $this->destination);
    
                    $Asin_record = [];
                    $product = [];
                    $product_lowest_price = [];
                    $count = 0;
                }
                $count++;
            }
            $table_name = table_model_create(country_code:$this->destination, model:'Asin_destination', table_name:'asin_destination_');
            $table_name->upsert($Asin_record, ['user_asin_unique'], ['asin', 'priority']);
            $push_to_bb->PushAsinToBBTable(product: $product, product_lowest_price: $product_lowest_price, country_code: $this->destination);
        }

    }
}
