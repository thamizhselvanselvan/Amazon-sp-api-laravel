<?php

namespace App\Console\Commands\Buybox_stores;

use Exception;

use League\Csv\Reader;
use App\Models\Aws_credential;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\buybox_sotres\product;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Services\buybox_stores\product_import;

class import_product_file_SPAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:import_product_file {seller_id}';

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

        $seller_id = $this->argument('seller_id');

        $aws = Aws_credential::with(['mws_region'])->where('seller_id', $seller_id)->where('api_type', 1)->first();
        $aws_key = $aws->id;
        $country_code = $aws->mws_region->region_code;
        $marketplace_id = $aws->mws_region->marketplace_id;

        $productreport = new product_import;
        $response = $productreport->getReports($aws_key, $country_code, $marketplace_id);

        if (array_key_exists('reports', $response) && count($response['reports']) > 0) {

            $report_id = $response['reports'][0]['reportId'];
            $report_document_id = $response['reports'][0]['reportDocumentId'];

            $result = $productreport->getReportDocumentByID($aws_key, $country_code, $report_document_id);



            if (array_key_exists('url', $result)) {

                $httpResponse = file_get_contents($result['url']);

                if (array_key_exists('compressionAlgorithm', $result)) {

                    $httpResponse = gzdecode($httpResponse);
                }
                Storage::put('/aws-products/products_' . $seller_id . '.txt', $httpResponse);
            }
            $this->insertdb($seller_id, $country_code);
            return true;
        }

        $response = $productreport->createReport($aws_key, $country_code, $marketplace_id);

        if (array_key_exists('reportId', $response)) {
            return $this->handle();
        }

        throw new Exception($response);
    }
    public function insertdb($seller_id, $country_code)
    {
   
        $records = CSV_Reader("/aws-products/products_" . $seller_id . ".txt", "\t");
        $cnt = 1;
        $asin_lists = [];
        foreach ($records as $key => $val) {
            $cnt++;
            $asin_lists[] = [
                'store_id' => $seller_id,
                'asin' => $val['asin1'],
                'store_price' => $val['price']
            ];
            if ($cnt == 10000) {

                product::upsert($asin_lists, ['asin', 'store_id'], ['store_price']);
                $cnt = 1;
                $asin_lists = [];
            }
        }
        // Artisan::call("mosh:price_priority_import $country_code");  //command will start crowling app 360 tables for Pricing
    }
}
