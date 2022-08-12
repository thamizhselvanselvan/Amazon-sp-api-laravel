<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\shipntrack\API\AmazonTrackingAPIController;

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
        ], function() {
        });
    });
    // Route::get('/testing', AmazonTrackingAPIController::class);
});

Route::get('testing', 'shipntrack\API\AmazonTrackingAPIController@index');
