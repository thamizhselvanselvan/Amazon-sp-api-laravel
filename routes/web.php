<?php

use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\User;
use App\Events\testEvent;
use App\Models\Mws_region;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use Dflydev\DotAccessData\Data;
use SellingPartnerApi\Endpoint;
use App\Models\Universal_textile;
use AWS\CRT\HTTP\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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


Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('/');

Auth::routes();

Route::get('login', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('login');
Route::get('home', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('home');



// Route::group(['middleware' => ['role:Admin', 'auth'], 'prefix' => 'admin'],function(){

// Route::get('dashboard', [App\Http\Controllers\Admin\HomeController::class, 'dashboard'])->name('admin.dashboard');

// });


Route::resource('/tests', 'TestController');

// $pdfParser = new Parser();
// $pdf = $pdfParser->parseFile('D:\laragon\www\amazon-sp-api-laravel\storage\app/US10000433.pdf');
// $content = $pdf->getText();
// $content = preg_split('/[\r\n|\t|,]/', $content, -1, PREG_SPLIT_NO_EMPTY);

// $unsetKey = array_search('Page 1 of 2', $content);
// unset($content[$unsetKey]);
// $content = array_values($content);
// dd($content);
// Bj69UT4UWy
// DB::table('Users')
// User::where('email', 'mudassir@moshecom.com')
//  ->update(['password' =>Hash::make('Bj69UT4UWy')]);

// $tables = DB::select('SHOW TABLES');
//    $tableCheck = 0;
//    // $testcount =0;
// //   dd($tables);4k
//        foreach ($tables as $table) {
//            $table = (array)($table);
//         $key = array_keys($table);
//            if ($table[$key[0]] == 'cargoclearance') {
//                $tableCheck = 1;
//                echo $key[0];
//                // $testcount++;
//            }
//        }
//      exit;
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
     $and = DB::connection('mssql')->select("SELECT DISTINCT Packet.AwbNo, Packet.CreatedDate FROM Packet Left JOIN KYCStatus on Packet.AwbNo = KYCStatus.AwbNo  where Packet.CreatedDate between '$starTime' and '$date 23:59:59' AND KYCStatus.AwbNo IS NULL");

     echo count($and);

     exit;
     $and = DB::connection('mssql')->select("SELECT DISTINCT Packet.AwbNo, Packet.CreatedDate FROM Packet INNER JOIN KYCStatus on Packet.AwbNo = KYCStatus.AwbNo  where Packet.CreatedDate between '$date 00:00:00' and '$date 23:59:59' ");
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

     echo 'kyc rejected ';
     $ans = DB::connection('mssql')->select("SELECT count(DISTINCT AwbNo) FROM KYCStatus where IsRejected = '1' AND (CreatedDate between '$date 00:00:00' and '$date 23:59:59')");
     po($ans);


     echo 'kyc Approved ';
     $ans = DB::connection('mssql')->select("SELECT count(DISTINCT AwbNo) FROM KYCStatus where IsRejected = '0' AND (CreatedDate between '$date 00:00:00' and '$date 23:59:59')");
     po($ans);

     exit;
});



Route::get('upload', function () {
     $file = 'D:\laragon\www\amazon-sp-api-laravel\storage\app/US10000135.pdf';
     // return file_get_contents($file);
     //     $fileName = (string) Str::uuid();
     $folder = config('filesystems.disks.do.folder');
     Storage::disk('do')->put(
          "/{$folder}/boe.pdf",
          file_get_contents($file)
     );

     // Storage::disk('do')->put('/boe.pdf', file_get_contents($file));
     echo 'success';
});

include_route_files(__DIR__ . '/pms/');
