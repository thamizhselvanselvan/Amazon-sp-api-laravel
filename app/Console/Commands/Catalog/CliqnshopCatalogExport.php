<?php

namespace App\Console\Commands\catalog;

use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CliqnshopCatalogExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Mosh:cliqnshop_catalog_export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports Catalog For Cliqnshop';

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
        Log::alert('Cliqnshop Command start');
        $cat_details = DB::connection('catalog')
        ->select("SELECT 
                cat.asin,
                cat.brand,
                cat.images,
                cat.item_name,
                price.usa_to_in_b2c as inprice 
            FROM catalognewuss AS cat
            join pricing_uss AS price
            on  cat.asin = price.asin
            LIMIT 100000         ");

        $headers = [
            'Category',
            'Sub-Category',
            'Brand',
            'ASIN',
            'Product Name',
            'short description',
            'long description',
            'Price',
            'price quantity',
            'price tax rate',
            'Attributese',
            'product variants',
            'Suggested Products',
            'Products bought together',
            'stock level',
            'date of back in stock',
            'Images1',
            'Images2',
            'Images3',
            'Images4',
            'Images5'
        ];
        $exportFilePath = 'Cliqnshop/catalog.csv';
        if (!Storage::exists($exportFilePath)) {
            Storage::put($exportFilePath, '');
        }
        $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
        $writer->insertOne($headers);

        foreach ($cat_details as $data) {

            $data = (array)$data;
            $imagedata = json_decode($data['images'], true);

            $img1 = [];
            if (isset($imagedata[0]['images'])) {

                foreach ($imagedata[0]['images'] as $counter => $image_data_new) {
                    $counter++;
                    if (array_key_exists("link", $image_data_new)) {
                        $img1["Images${counter}"] = $image_data_new['link'];
                    }   

                    if ($counter == 5) {
                        break;
                    }
                }
            }


            $csv_array = [
                'Category' => null,
                'Sub-Category' => null,
                'Brand' => ucfirst($data['brand']),
                'ASIN' => $data['asin'],
                'Product Name' => $data['item_name'],
                'short description' => null,
                'long description' => null,
                'Price' => $data['inprice'],
                'price quantity' => '1',
                'price tax rate' => '19',
                'Attributese' => null,
                'product variants' => null,
                'Suggested Products' => null,
                'Products bought together' => null,
                'stock level' => '500',
                'date of back in stock' => null
            ];

            $csv_array = [...$csv_array, ...$img1];

            $writer->insertOne($csv_array);
        }
        Log::alert('Cliqnshop Command ended');
    }
}
