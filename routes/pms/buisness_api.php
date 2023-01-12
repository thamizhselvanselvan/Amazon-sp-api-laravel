<?php

use League\Csv\Reader;
use App\Models\Aws_credential;
use Illuminate\Support\Facades\DB;
use App\Models\buybox_sotres\product;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\AWS_Business_API\AWS_POC\Orders;
use App\Http\Controllers\PMSPHPUnitTestController;
use App\Http\Controllers\Cliqnshop\ImageBrandController;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Search;

use App\Http\Controllers\BuisnessAPI\ProductsRequestController;
use App\Services\AWS_Business_API\Details_dump\product_details;
use App\Services\AWS_Business_API\AWS_POC\Search_Product_Request;
use App\Services\AWS_Business_API\Search_Product_API\Search_Product;

Route::get('product/details', 'BuisnessAPI\SearchProductRequestController@searchproductRequest');
Route::resource('business/search/products', 'BuisnessAPI\SearchProductRequestController');

Route::get('buisness/product/details', 'BuisnessAPI\ProductsRequestController@productRequestasin');
Route::resource('business/products/request', 'BuisnessAPI\ProductsRequestController');

Route::get('buisness/product/offers', 'BuisnessAPI\searchOffersRequestController@searchoffersproduct');
Route::resource('business/offers', 'BuisnessAPI\searchOffersRequestController');

Route::get('business/asin/details', 'BuisnessAPI\GetProductsByAsinsController@searchasinproduct');
Route::resource('business/byasins', 'BuisnessAPI\GetProductsByAsinsController');

Route::resource('business/details', 'BuisnessAPI\ProductDetailsController');
Route::get('buisness/details', 'BuisnessAPI\ProductDetailsController@viewpro');

Route::get('business/orders/details', 'BuisnessAPI\OrdersController@orderpending')->name('business.orders.pending.list');
Route::get('business/orders/view', 'BuisnessAPI\OrdersController@getorders');
Route::get('business/orders/pending', 'BuisnessAPI\OrdersController@orderspending');
Route::get('business/offers_view', 'BuisnessAPI\OrdersController@prodoffers');
Route::get('business/order/book', 'BuisnessAPI\OrdersController@orderbooking');

Route::get('business/booked/details', 'BuisnessAPI\OrdersController@booked');
Route::get('business/booked/list', 'BuisnessAPI\OrdersController@booked')->name('business.orders.booked.list');

Route::get('business/orders/confirm', 'BuisnessAPI\OrdersController@confirmation');
Route::get('business/orders/confirm/list', 'BuisnessAPI\OrdersController@confirmation')->name('business.orders.confirm.list');
Route::get('business/ship/confirmation', 'BuisnessAPI\OrdersController@notification');
Route::get('business/orders/shipment/list', 'BuisnessAPI\OrdersController@notification')->name('business.orders.shipment.list');

Route::resource('business/orders', 'BuisnessAPI\OrdersController');

Route::get('cliqnshop/kyc', 'Cliqnshop\CliqnshopKycController@kyc_index')->name('cliqnshop.kyc');
Route::get('cliqnshop/kyc/details', 'Cliqnshop\CliqnshopKycController@kyc_details')->name('cliqnshop.kyc.view');
Route::get('cliqnshop/kyc/update', 'Cliqnshop\CliqnshopKycController@kyc_status')->name('cliqnshop.kyc.update');

Route::get('cliqnshop/contact', 'Cliqnshop\ContactListController@contactlist')->name('cliqnshop.contacted');
Route::get('cliqnshop/contact/list', 'Cliqnshop\ContactListController@contactlist')->name('cliqnshop.contacted.list');

Route::get('cliqnshop/banner', 'Cliqnshop\ImageBrandController@threebanner')->name('cliqnshop.banner');
Route::post('cliqnshop/banner/image', 'Cliqnshop\ImageBrandController@storeimage')->name('cliqnshop.image.store');

Route::get('cliqnshop/brand', 'Cliqnshop\ImageBrandController@topselling')->name('cliqnshop.brand');
Route::post('cliqnshop/brand/store', 'Cliqnshop\ImageBrandController@storeasin')->name('cliqnshop.brand.store');

Route::get('cliqnshop/two_banners', 'Cliqnshop\ImageBrandController@twobannersection')->name('cliqnshop.twobanners');
Route::post('cliqnshop/2banners/store', 'Cliqnshop\ImageBrandController@two_bannerstore')->name('cliqnshop.two.banner.store');

Route::get('cliqnshop/one_banners', 'Cliqnshop\ImageBrandController@onebanner')->name('cliqnshop.onebanner');
Route::post('cliqnshop/1banners/store', 'Cliqnshop\ImageBrandController@one_bannerstore')->name('cliqnshop.one.banner.store');

Route::get('cliqnshop/trending', 'Cliqnshop\ImageBrandController@trendingbrandssection')->name('cliqnshop.trending');
Route::post('cliqnshop/trending/store', 'Cliqnshop\ImageBrandController@trendingbrands')->name('cliqnshop.trending.store');

Route::get('cliqnshop/promo', 'Cliqnshop\ImageBrandController@promobanner')->name('cliqnshop.promo.banner');
Route::post('cliqnshop/promo/store', 'Cliqnshop\ImageBrandController@promostore')->name('cliqnshop.promo.banner.store');



Route::get('cliqnshop/catalog/csv', 'Catalog\CliqnshopCatalogController@asinCsvDownload')->name('cliqnshop.catalog.csv.templete');
Route::post('cliqnshop/catalog/csv/import', 'Catalog\CliqnshopCatalogController@cliqnshopImport')->name('cliqnshop.catalog.csv.import');
Route::get('catalog/cliqnshop/new_asin', 'Catalog\CliqnshopCatalogController@uploaded_export_download')->name('uploaded.asin.catalog.export.cliqnshop');
Route::get('uploaded/catalog/cliqnshop/download/{index}', 'Catalog\CliqnshopCatalogController@Download_uploaded_asin_catalog')->name('uploaded.asin.catalog.export.cliqnshop.download');
Route::get('cliqnshop/db/upload', 'Catalog\CliqnshopCatalogController@insertCliqnshop')->name('cliqnshop.catalog.db.upload');

// Route::get('product/test', function () {
//   // $data[] = $key;
//   $searchKey = 'iPhone';
//   $siteId = '4.';
//   $source = 'uae';
//   $ApiCall = new Search_Product();
//   $result = $ApiCall->SearchProductByKey($searchKey, $siteId, $source);
//   po($result);
// });
Route::get('test', function () {
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


Route::get('ty', function () {
    exit;
    $data  = ['7', '8', '9', '10', '12',  '27'];
    foreach ($data as $store_id) {

        $csv = Reader::createFromPath(Storage::path('/aws-products/products_' . $store_id . '.txt'), 'r');
        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);


        foreach ($csv as $key => $val) {
            $data = [
                'store_id' => $store_id,
                'asin' => $val['asin1'],
                'store_price' => $val['price'],
                'cyclic' => '0'
            ];
            product::upsert($data, ['asin', 'store_id'], ['store_price', 'cyclic']);
        }
    }
});
