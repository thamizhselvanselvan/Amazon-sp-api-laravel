<?php

namespace App\Console\Commands;

use App\Models\Admin\BB\BB_Product;
use App\Models\seller\AsinMasterSeller;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class sellerAsinRemove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:seller-asin-remove {seller_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seller Asin Remove';

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
        $path = 'Seller/Remove/AsinMaster/remove_asin.csv';
        $seller_id = $this->argument('seller_id');
        // $seller_id = 1;

        $csv = Reader::createFromPath(Storage::path($path), 'r');
        $csv->setDelimiter(",");
        $csv->setHeaderOffset(0);

        $stmt = (new Statement())
            ->where(function (array $record) {
                return $record;
            })
            ->offset(0);

        $records = $stmt->process($csv);

        $asin_masters = [];
        $product = [];
        $product_lowest_price = [];


        $count = 1;
        $tagger = 1;
        $country_code = '';
        foreach ($records as $key => $record) {
            $asin = $record['ASIN'];
            $country_code = $record['Destination'];

            $asin_masters[$tagger][$seller_id][] = [
                'asin' => $asin
            ];

            $product[] = [
                'seller_id' => $seller_id,
                'asin1' => $asin,
                'country_code' => $country_code,
            ];

            if ($count == 3000) {
                $tagger++;
                $count = 0;
            }
            $count++;
        }

        foreach ($asin_masters as $key => $asin_master) {

            foreach ($asin_master as $seller_id => $asins) {

                AsinMasterSeller::whereIn('asin', $asins)->where('seller_id', $seller_id)->delete();
                $bb_product = table_model_set($country_code, 'BB_Product', 'product');
                $bb_product->whereIn('asin1', $asins)->where('seller_id', $seller_id)->delete();
            }
        }
        Log::warning("asin delete successfully");
    }
}
