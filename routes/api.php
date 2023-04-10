<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\shipntrack\API;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api\App\v1', 'as' => 'v1.', 'prefix' => 'v1'], function () {
    Route::group(['prefix' => 'pub'], function () {
        Route::apiResource('/testGetApi', 'ApiTestController');
    });

    Route::middleware('auth:api')->group(function () {
        Route::group([
            'prefix' => 'auth',
            'middleware' => \Fruitcake\Cors\HandleCors::class,
        ], function () {
        });
    });
    // Route::get('/testing', AmazonTrackingAPIController::class);
});

$method = ['get', 'post'];
Route::match($method, 'trackingAmazon', 'shipntrack\API\AmazonTrackingAPIController@AmazonTrackingMaster');
Route::match($method, 'trackingb2cship', 'shipntrack\API\B2CShipTrackingAPIController@B2CshipTrackingResponse');

Route::match($method, 'test/zoho/webhook', 'TestController@zohoWebhookResponse');

Route::match($method, 'product', 'Catalog\CliqnshopCatalogController@CliqnshopProductSearchRequest');

Route::match($method, 'zoho/webhook', 'Zoho\ZohoCRMController@ZohoWebhook');

Route::match($method, 'b2cship/tracking/{awbNo}', 'shipntrack\API\B2CShipTrackingAPIController@B2CShipTrackingAPI');
