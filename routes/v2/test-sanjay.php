<?php

use Illuminate\Support\Facades\Route;

Route::get('mosh/test/order/{order_id}/{seller_id}/{country_code}', 'TestController@getOrder');
