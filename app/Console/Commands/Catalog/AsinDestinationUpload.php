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
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:Asin-destination-upload {user_id}';

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
        $path = 'AsinDestination/asin.csv';
        $asins = Reader::createFromPath(Storage::path($path), 'r');
        $asins->setHeaderOffset(0);

        $source = buyboxCountrycode();

        $Asin_record = [];
        $product = [];
        $product_lowest_price = [];

        $count = 0;
        foreach ($asins as  $asin_details) {

            $count = 0;
            $asin = $asin_details['ASIN'];
            $destination =  $asin_details['Destination'];

            $Asin_record[] = [
                'asin'  => $asin,
                'user_id'   => $user_id,
                'destination' => $destination,
            ];

            $product[] = [
                'seller_id' => $source[$destination],
                'active' => 1,
                'asin1' => $asin,
            ];

            $product_lowest_price[] = [
                'asin' => $asin,
                'import_type' => 'Seller',
            ];

            if ($count == 1000) {

                AsinDestination::upsert($Asin_record, ['user_asin_destination_unique'], ['destination']);
                $push_to_bb->PushAsinToBBTable(product: $product, product_lowest_price: $product_lowest_price, country_code: $destination);

                $Asin_record = [];
                $product = [];
                $product_lowest_price = [];
            }
            $count++;
        }

        AsinDestination::upsert($Asin_record, ['user_asin_destination_unique'], ['destination']);
        $push_to_bb->PushAsinToBBTable(product: $product, product_lowest_price: $product_lowest_price, country_code: $destination);

    }
}
