<?php

namespace App\Console\Commands\Buybox_stores;

use Exception;

use League\Csv\Reader;
use App\Models\Aws_credential;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Product;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Models\order\OrderSellerCredentials;
use App\Services\Buybox_stores\product_import;

class import_product_file_SPAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:import_product_file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aws Store Product file using SP API';

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
       // $stores_id  = [6, 7, 8, 9, 10, 11, 12, 20, 27, 44];
      
        $stores_id =  OrderSellerCredentials::query()->select('seller_id', 'country_code')->where('buybox_stores', 1)->get();

        foreach ($stores_id as $data) {

            try {
                
                $seller_id = $data['seller_id'];
                //$seller_id = $data;

                Log::alert('store' .  ' ' . $seller_id);

                $aws = Aws_credential::with(['mws_region'])->where('seller_id', $seller_id)->where('api_type', 1)->first();

                $aws_key = $aws->id;
                $country_code = $aws->mws_region->region_code;
                $marketplace_id = $aws->mws_region->marketplace_id;

                $productreport = new product_import;
                $response = $productreport->getReports($aws_key, $country_code, $marketplace_id);

                if (array_key_exists('reports', $response) && count($response['reports']) > 0) {

                    $report_document_id = $response['reports'][0]['reportDocumentId'];

                    $result = $productreport->getReportDocumentByID($aws_key, $country_code, $report_document_id);

                    if (array_key_exists('url', $result)) {

                        $httpResponse = file_get_contents($result['url']);

                        if (array_key_exists('compressionAlgorithm', $result)) {

                            $httpResponse = gzdecode($httpResponse);
                        }

                        Storage::put('/aws-products/aws-store-files/products_' . $seller_id . '.txt', $httpResponse);

                    }

                    $this->insertdb($seller_id);
                }
            } catch (Exception $e) {
                Log::notice('Store File Not Found' . ' ' . $seller_id);
            }
        } // END of Foreach Loop

    }

    public function insertdb($seller_id): void
    {

        $records = CSV_Reader("/aws-products/aws-store-files/products_" . $seller_id . ".txt", "\t");

        $cnt = 1;
        $asin_lists = [];

        foreach ($records as $record) {

            $asin_lists[] = [
                'store_id' => $seller_id,
                'asin' => $record['asin1'],
                'product_sku' => $record['seller-sku'],
                'store_price' => $record['price'],
                'cyclic' => 0
            ];

            if ($cnt == 5000) {

                $this->product_upsert_query(asin_lists: $asin_lists);

                $cnt = 1;
                $asin_lists = [];
            }

            $cnt++;
        }

        if (count($asin_lists) > 0) {

            $this->product_upsert_query(asin_lists: $asin_lists);
        }
    }

    public function product_upsert_query(array $asin_lists)
    {

        return Product::upsert($asin_lists, ['asin', 'store_id'], ['store_price', 'product_sku', 'cyclic']);
    }
}
