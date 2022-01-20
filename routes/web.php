<?php


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


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
Route::get('/spapitest','viewPageController@spapitest')->name('spapi');

Route::get('/', function () {
    return view('welcome');
});
/*
    CatlogApi
*/
Route::get('/view','CatlogApiController@index')->name('show');
Route::post('/showInput','CatlogApiController@show')->name(('showInput'));

/*
    productPricing
    ->CompetitivePricing
*/
Route::get('/pricing','productPricing\CompetitivePricingController@index')->name('productPricing.getCompetitivePricing');
Route::get('/getPrice','productPricing\CompetitivePricingController@show')->name('getPrice');

/* productPricing
    ->Itemoffers
*/
Route::get('/itemoffer','productPricing\ItemOfferController@index')->name('productPricing.show');
Route::post('/getItemOffer','productPricing\ItemOfferController@show')->name('getItemOffer');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/test/getCatalogItem', [App\Http\Controllers\TestController::class, 'getCatalogItem'])->name('getCatalogItem');
Route::get('/test/getCompetitivePricing', [App\Http\Controllers\TestController::class, 'getCompetitivePricing'])->name('getCompetitivePricing');
Route::get('/test/getItemOffers', [App\Http\Controllers\TestController::class, 'getItemOffers'])->name('getItemOffers');
Route::get('/test/getPricing', [App\Http\Controllers\TestController::class, 'getPricing'])->name('getPricing');

Route::get('/info', function () {
    phpinfo();
});

Route::get('/tests', function () {
    

});



Route::get('/test', function () {

    
   
});
