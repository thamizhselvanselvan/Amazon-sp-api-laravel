<?php

use RedBeanPHP\R;
use App\Models\User;
use App\Events\testEvent;
use App\Models\Mws_region;
use Maatwebsite\Excel\Row;
use SellingPartnerApi\Endpoint;
use App\Models\universalTextile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use SellingPartnerApi\Api\ProductPricingApi;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('/');

Auth::routes();

Route::get('login', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('login');
Route::get('home', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('home');

// Route::group(['middleware' => ['role:Admin', 'auth'], 'prefix' => 'admin'],function(){

// Route::get('dashboard', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('admin.dashboard');

// });

Route::resource('admin/mws_regions', 'Admin\RegionController');
Route::get('admin/credentials', 'Admin\CredentialsController@index');
Route::get('admin/currencys', 'Admin\CurrencyController@index');
Route::get('admin/rolespermissions', 'Admin\RolesPermissionsController@index');

Route::get('admin/user_list', 'Admin\AdminManagementController@index')->name('admin.user_list');
Route::get('admin/password_reset_view/{id}', 'Admin\AdminManagementController@password_Change_view');
Route::post('admin/password_reset_save/{id}', 'Admin\AdminManagementController@password_reset_save')->name('admin.password_reset_save');

Route::get('admin/catalog_user', 'Admin\CatalogManagementController@index')->name('admin.catalog_user');
Route::get('admin/catalog/{id}/password_reset', 'Admin\CatalogManagementController@password_reset_view');
Route::post('admin/catalog/{id}/password_reset_save', 'Admin\CatalogManagementController@password_reset_save')->name('catalog.password_reset_save');
Route::get('admin/catalog/{id}/edit', 'Admin\CatalogManagementController@edit_view');
Route::post('admin/catalog/{id}/update', 'Admin\CatalogManagementController@update')->name('catalog_user.update');
Route::get('admin/catalog/create','Admin\CatalogManagementController@create')->name('catalog_user.create');
Route::post('admin/catalog/user_save', 'Admin\CatalogManagementController@user_save')->name('catalog_user_save');

Route::get('asin-master', 'AsinMasterController@index');
Route::get('add-asin', 'AsinMasterController@addAsin');
Route::get('import-bulk-asin', 'AsinMasterController@importBulkAsin');
Route::get('export-asin', 'AsinMasterController@exportAsinToCSV');
Route::post('add-bulk-asin', 'AsinMasterController@addBulkAsin');
Route::get('asinMaster_download', 'filedownloads\FileDownloadsController@download_asin_master')->name('download.asinMaster');

Route::resource('textiles', 'textilesController');
Route::post('import-csv', 'textilesController@importTextiles')->name('import.csv');
Route::get('export_to_csv', 'textilesController@exportTextilesToCSV')->name('export.csv');

Route::get('file_downloads', 'filedownloads\FileDownloadsController@filedownloads')->name('file.downloads');
Route::get('other_file_download', 'filedownloads\FileDownloadsController@other_file_download')->name('file.other_file_download');
Route::get('universalTextiles_download', 'filedownloads\FileDownloadsController@download_universalTextiles')->name('download.universalTextiles');

Route::get('product/amazon_com', 'product\productController@index')->name('product.amazon_com');
Route::get('product/fetch_from_amazon', 'product\productController@fetchFromAmazon')->name('product.fetch.amazon');
Route::get('product/getPricing', 'product\productController@amazonGetPricing')->name('amazon.getPricing');

Route::get('other-product/amazon_com', 'otherProduct\anotherAmazonProductController@index')->name('product.amazon_com');
Route::post('other-product/export', 'otherProduct\anotherAmazonProductController@exportOtherProduct')->name('export.other-product');
Route::get('other-product/download/{id}', 'filedownloads\FileDownloadsController@download_other_product')->name('download.other-product');

Route::get('b2cship/kyc', 'b2cship\b2cshipKycController@index');



Route::get('path', function () {

     $file_path = "excel/downloads/universalTextilesExport.csv";
     echo Storage::path($file_path);
     echo "<hr>";
     echo "Base Path:- ";
     echo base_path();

     echo "<hr>";
     echo 'saving path :- ';
     $file_path = "excel\\downloads\\universalTextilesExport.csv";
     echo Storage::path($file_path);

     //echo Str;
});


Route::resource('/tests', 'TestController');

Route::get('updatePassword', function(){
     // Bj69UT4UWy
     // DB::table('Users')
     // User::where('email', 'mudassir@moshecom.com')
     //  ->update(['password' =>Hash::make('Bj69UT4UWy')]);

     po(User::get());


});

Route::get('/test', function () {

     $ans = event(new testEvent('hello world'));
     po($ans);
     exit;

     $path = 'universalTextilesImport/textiles.csv';

     return Storage::url($path);

     return ('downloaded done');
});

Route::get('/amazon_count', 'TestController@index');

Route::get('/asin/{asin}/{code}', 'TestController@getASIN');

Route::get("mssql", function () {
     $ans = DB::connection('mssql')->select("SELECT TOP 5 * FROM Apilog");
     po($ans);
     exit;
     $B2CShipEventMapping = DB::connection('mssql')->select("SELECT TOP 50 * FROM B2CShipEventMapping");
     $TrackingErrorMapping = DB::connection('mssql')->select("SELECT TOP 50 * FROM TrackingErrorMapping");
     $TrackingErrorMaster = DB::connection('mssql')->select("SELECT TOP 50 * FROM TrackingErrorMaster");
     $TrackingEventMapping = DB::connection('mssql')->select("SELECT TOP 50 * FROM TrackingEventMapping");
     $TrackingEventMaster = DB::connection('mssql')->select("SELECT TOP 50 * FROM TrackingEventMaster");

     po($B2CShipEventMapping);
     po($TrackingErrorMapping);
     po($TrackingErrorMaster);
     po($TrackingEventMapping);
     po($TrackingEventMaster);
});

Route::get('info', function(){
     phpinfo();
});
Route::get("pricing", function () {

     $india_token = "Atzr|IwEBIJbccmvWhc6q6XrigE6ja7nyYj962XdxoK8AHhgYvfi-WKo3MsrbTSLWFo79My_xmmT48DSVh2e_6w8nxgaeza9XZ9HtNnk7l4Rl_nWhhO6xzEdfIfU7Ev4hktjvU8CjMvYnRn_Cw5JveEqZSggp961Sg7CoBEDpwXZbAE3SYXSdeNxfP2Nu84y2ZzlsP3CNZqcTvXMWflLk1qqY6ittwlGAXpL0BwGxPCBRmjbXOy5xsZqwCPAQhW6l9AJtLPhwOlSSDjcxxvCTH9-LEPSWHLRP1wV3fRgosOlCsQgmuET0pm5SO7FVJTRWux8h2k5hnnM";
     $usa_token = "Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg";
     $config = new Configuration([
          "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
          "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
          "lwaRefreshToken" => $usa_token,
          "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
          "awsSecretAccessKey" => "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",
          "endpoint" => Endpoint::NA,  // or another endpoint from lib/Endpoints.php
          "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role'
     ]);

     $apiInstance = new ProductPricingApi($config);
     $marketplace_id_india = 'A21TJRUUN4KGV'; // string | A marketplace identifier. Specifies the marketplace for which prices are returned.
     $marketplace_id_usa = 'ATVPDKIKX0DER'; // string | A marketplace identifier. Specifies the marketplace for which prices are returned.
     $item_type = 'Asin'; // string | Indicates whether ASIN values or seller SKU values are used to identify items. If you specify Asin, the information in the response will be dependent on the list of Asins you provide in the Asins parameter. If you specify Sku, the information in the response will be dependent on the list of Skus you provide in the Skus parameter.
     $asins = ['B0000632EN']; // string[] | A list of up to twenty Amazon Standard Identification Number (ASIN) values used to identify items in the given marketplace.
     $skus = array(); // string[] | A list of up to twenty seller SKU values used to identify items in the given marketplace.
     $item_condition = 'New'; // string | Filters the offer listings based on item condition. Possible values: New, Used, Collectible, Refurbished, Club.
     $offer_type = 'B2C'; // string | Indicates whether to request pricing information for the seller's B2C or B2B offers. Default is B2C.


     print_r($asins);

     try {
          $result = $apiInstance->getCompetitivePricing($marketplace_id_usa, $item_type, $asins)->getPayload();
          po($result);
     } catch (Exception $e) {
          echo 'Exception when calling ProductPricingApi->getPricing: ', $e->getMessage(), PHP_EOL;
     }
});

include_route_files(__DIR__ . '/pms/');
