<?php

namespace App\Console\Commands\catalog;

use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CliqnshopCatalogExport extends Command
{
    private $offset = 0;
    private $count = 1;
    private $remender;
    private $writer;
    private $file_path;

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
        $total_csv = 10000;
        $chunk = 1000;
        $offset = 0;
        $this->remender = $total_csv / $chunk;
        $header = [
            'catalognewuss.asin',
            'catalognewuss.brand',
            'catalognewuss.images',
            'catalognewuss.item_name',
            'pricing_uss.usa_to_in_b2c',
            'pricing_uss.us_price',
            'pricing_uss.usa_to_uae',

        ];
        $asin_cat = 'pricing_uss';

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'catalognew');


        $cat_details =  $table_name->select($header)
            ->join($asin_cat, 'catalognewuss.asin', '=', $asin_cat . '.asin')
            ->chunk($chunk, function ($result) use ($header) {

                if ($this->count == 1) {
                    $headers = [
                        'Category',
                        'Sub-Category',
                        'Brand',
                        'ASIN',
                        'Product Name',
                        'short description',
                        'long description',
                        'Price_US_IN',
                        'Price_US_US',
                        'Price_US_UAE',
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

                    $this->file_path = "Cliqnshop/" . "CatalogCliqnshop" . $this->offset . ".csv";
                    if (!Storage::exists($this->file_path)) {
                        Storage::put($this->file_path, '');
                    }
                    $this->writer = Writer::createFromPath(Storage::path($this->file_path, 'w'));
                    $this->writer->insertOne($headers);
                }
                foreach ($result as $data) {

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
                        'Price_US_IN' => $data['usa_to_in_b2c'],
                        'Price_US_US' => $data['us_price'],
                        'Price_US_UAE' => $data['usa_to_uae'],
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
                    $this->writer->insertOne($csv_array);
                }

                if ($this->remender == $this->count) {
                    ++$this->offset;

                    $this->count = 1;
                } else {
                    ++$this->count;
                }
            });


        }
}
