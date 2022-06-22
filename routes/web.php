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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use App\Services\SP_API\API\Catalog;
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

     dd(User::get());

     exit;

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
    
    if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

        Log::warning("Export asin command executed local !");
        $base_path = base_path();
        $command = "cd $base_path && php artisan pms:seller-order-item-import > /dev/null &";
        exec($command);
    } else {

        Artisan::call('pms:seller-order-item-import ');
    }
});

Route::get('order/catalog', function()
{
    
    $order_item_details = DB::connection('order')->select("SELECT seller_identifier, asin, country from orderitemdetails where status = 0 ");
        $count = 0;
        $batch = 0;
        $asinList = [];
        foreach ($order_item_details as $key => $value) {
            $asin = $value->asin;
            // $check = DB::connection('catalog')->select("SELECT asin from catalog where asin = '$asin'");
            $check = [];
            if (!array_key_exists('0', $check)) {
                // $asinList[$count]->asin = $asin;
                $count++;
                $batch++;
                $data[] = $value;
            }
            
            //$type = 1 for seller, 2 for Order, 3 for inventory
            if ($count == 10) {
                $count = 0;
                $type = 2;
                $catalog = new Catalog();
                $catalog->index($data, NULL, $type, $batch);
                Log::alert('10 asin imported');
                $data = [];
                exit;
            }
        }
});

Route::get('/', 'Auth\LoginController@showLoginForm')->name('/');
Auth::routes();
Route::get('login', 'Admin\HomeController@dashboard')->name('login');
Route::get('home', 'Admin\HomeController@dashboard')->name('home');
Route::resource('/tests', 'TestController');
Route::get('test/seller', 'TestController@SellerTest');
Route::get('/asin/{asin}/{code}', 'TestController@getASIN');

include_route_files(__DIR__ . '/pms/');
