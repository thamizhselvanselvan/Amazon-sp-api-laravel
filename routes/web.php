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
Route::get('/showInput','CatlogApiController@show')->name(('showInput'));

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
Route::get('/getItemOffer','productPricing\ItemOfferController@show')->name('getItemOffer');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
