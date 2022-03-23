<?php

use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\User;
use App\Events\testEvent;
use App\Models\Mws_region;
use Maatwebsite\Excel\Row;
use Dflydev\DotAccessData\Data;
use SellingPartnerApi\Endpoint;
use App\Models\universalTextile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
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
Route::get('admin/user/create','Admin\AdminManagementController@create')->name('add_user.create');
Route::post('admin/admin/save_user', 'Admin\AdminManagementController@save_user')->name('admin_save_user');

Route::get('admin/catalog_user', 'Admin\CatalogManagementController@index')->name('admin.catalog_user');
Route::get('admin/catalog/{id}/password_reset', 'Admin\CatalogManagementController@password_reset_view');
Route::post('admin/catalog/{id}/password_reset_save', 'Admin\CatalogManagementController@password_reset_save')->name('catalog.password_reset_save');
Route::get('admin/catalog/{id}/edit', 'Admin\CatalogManagementController@edit_view');

Route::post('admin/catalog/{id}/update', 'Admin\CatalogManagementController@update')->name('catalog_user.update');
Route::get('admin/catalog/create','Admin\CatalogManagementController@create')->name('catalog_user.create');
Route::post('admin/catalog/user_save', 'Admin\CatalogManagementController@user_save')->name('catalog_user_save');
Route::delete('admin/catalog/{id}/user_delete','Admin\CatalogManagementController@trash')->name('catalog_user_delete');

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

Route::get('B2cship/kyc', 'B2cship\B2cshipKycController@index');
Route::get('B2cship/tracking_status/details', 'B2cship\TrackingStatusController@trackingStatusDetails');
Route::get('B2cship/booking', 'B2cship\B2cshipbookingController@Bookingstatus');

Route::resource('/tests', 'TestController');


     // Bj69UT4UWy
     // DB::table('Users')
     // User::where('email', 'mudassir@moshecom.com')
     //  ->update(['password' =>Hash::make('Bj69UT4UWy')]);

Route::get('/asin/{asin}/{code}', 'TestController@getASIN');


Route::get("b2cship", function () {

     $starTime = Carbon::today();
     echo $starTime;
     $endTime = Carbon::now();
     echo $endTime;
     $date = $starTime->toDateString();
     
     exit;
     // $ans = DB::connection('mssql')->select("SELECT Top 5 * FROM KYCStatus ");
     // po($ans);
     // exit;

     echo ' yesterday Total KYC pending :- ';
     $and = DB::connection('mssql')->select("SELECT DISTINCT Packet.AwbNo, Packet.CreatedDate FROM Packet Left JOIN KYCStatus on Packet.AwbNo = KYCStatus.AwbNo  where Packet.CreatedDate between '$starTime' and '$date 23:59:59' AND KYCStatus.AwbNo IS NULL" );

     echo count($and);

     exit;
     $and = DB::connection('mssql')->select("SELECT DISTINCT Packet.AwbNo, Packet.CreatedDate FROM Packet INNER JOIN KYCStatus on Packet.AwbNo = KYCStatus.AwbNo  where Packet.CreatedDate between '$date 00:00:00' and '$date 23:59:59' " );
//     echo count($and);

     echo '<br>';
     echo 'yesterday total packet booked :- ';
     $ans = DB::connection('mssql')->select("SELECT AwbNo FROM Packet where CreatedDate between '$date 00:00:00' and '$date 23:59:59'");
     echo count($ans);
    
     echo '<br>';
// exit;
     // echo 'total kyc status ' ;
     // $ans = DB::connection('mssql')->select("SELECT count(DISTINCT AwbNo) FROM KYCStatus where CreatedDate between '$date 00:00:00' and '$date 23:59:59'");
     // po($ans);
    
    echo 'kyc rejected ' ;
     $ans = DB::connection('mssql')->select("SELECT count(DISTINCT AwbNo) FROM KYCStatus where IsRejected = '1' AND (CreatedDate between '$date 00:00:00' and '$date 23:59:59')");
     po($ans);
     

    echo 'kyc Approved ' ;
     $ans = DB::connection('mssql')->select("SELECT count(DISTINCT AwbNo) FROM KYCStatus where IsRejected = '0' AND (CreatedDate between '$date 00:00:00' and '$date 23:59:59')");
     po($ans);
     
     exit;
});


include_route_files(__DIR__ . '/pms/');
