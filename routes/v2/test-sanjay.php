<?php

use Carbon\Carbon;
use App\Services\Zoho\ZohoApi;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;

Route::get('mosh/test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');

