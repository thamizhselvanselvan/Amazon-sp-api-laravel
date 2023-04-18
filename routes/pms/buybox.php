<?php

use League\Csv\Reader;
use App\Models\Aws_credential;
use App\Models\Buybox_stores\Product;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\PMSPHPUnitTestController;
use App\Http\Controllers\Cliqnshop\ImageBrandController;

Route::prefix('buybox/')->group(function () {

    Route::get('asin','buybox\BuyboxAsinMasterController@index');
});

Route::get('sanju/test/bb', function () {
    exit;
    $seller_id = '8';
    $aws = Aws_credential::with(['mws_region'])->where('seller_id', $seller_id)->where('api_type', 1)->first();
    $aws_key = $aws->id;
    $country_code = $aws->mws_region->region_code;
    $marketplace_id = $aws->mws_region->marketplace_id;


    $productreport = new ImageBrandController;
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
            Storage::put('/aws-products/attempt_' . $seller_id . '.txt', $httpResponse);
        }

        return true;
    }

    $response = $productreport->createReport($aws_key, $country_code, $marketplace_id);

    if (array_key_exists('reportId', $response)) {
        return $this->handle();
    }

    throw new Exception($response);
});


Route::get('sanju/test/bb_read', function () {

    // $data  = ['7', '8', '9', '10', '12',  '27'];
    $data  = ['7'];
    foreach ($data as $store_id) {

        $csv = Reader::createFromPath(Storage::path('/aws-products/aws-store-files/products_' . $store_id . '.txt'), 'r');
        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);


        foreach ($csv as $key => $val) {
            // po($val['seller-sku']);
            $data = [
                'store_id' => $store_id,
                'asin' => $val['asin1'],
                'product_sku' => $val['seller-sku'],
                'store_price' => $val['price'],
                'cyclic' => '0'
            ];
            Product::upsert($data, ['asin', 'store_id'], ['store_price', 'product_sku', 'cyclic']);
        }
    }
});
