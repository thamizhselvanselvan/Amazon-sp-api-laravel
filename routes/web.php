<?php

use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\User;
use League\Csv\Reader;
use App\Events\testEvent;
use AWS\CRT\HTTP\Request;
use App\Models\Mws_region;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use Dflydev\DotAccessData\Data;
use SellingPartnerApi\Endpoint;
use App\Models\Inventory\Shelve;
use App\Models\Universal_textile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TestController;
use SellingPartnerApi\Api\ProductPricingApi;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
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
Route::get('pdf',function(){

     $url = 'https://amazon-sp-api-laravel.test/admin/rolespermissions';
     $file_path = 'product/label.pdf';

     if (!Storage::exists($file_path)) {
         Storage::put($file_path, '');
     }

     $exportToPdf = Storage::path($file_path);
         Browsershot::url($url)
         ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
         ->showBackground()
         ->savePdf($exportToPdf);

         return Storage::download($exportToPdf);
});

Route::get('command', function () {

     Artisan::call('pms:country-state-city');
});

Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('/');
Auth::routes();
Route::get('login', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('login');
Route::get('home', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('home');
// Route::group(['middleware' => ['role:Admin', 'auth'], 'prefix' => 'admin'],function(){
// Route::get('dashboard', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('admin.dashboard');
// });
Route::resource('/tests', 'TestController');
Route::get('test/seller', 'TestController@SellerTest');
Route::get('/asin/{asin}/{code}', 'TestController@getASIN');

include_route_files(__DIR__ . '/pms/');
