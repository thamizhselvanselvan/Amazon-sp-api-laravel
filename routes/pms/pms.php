<?php

use App\Http\Controllers\RobinTest;
use Illuminate\Support\Facades\Route;

Route::get('list', [RobinTest::class, 'listUrls']);

Route::get('robin/env', [RobinTest::class, 'showEnv']);
Route::get('robin/server', [RobinTest::class, 'showServer']);
Route::get('robin/config', [RobinTest::class, 'showConfig']);
Route::get('robin/info', [RobinTest::class, 'showPHPInfo']);

Route::get('robin/redis', [RobinTest::class, 'redisTest']);
Route::get('robin/mem', [RobinTest::class, 'memTest']);
Route::get('robin/system-config', [RobinTest::class, 'systemConfigCacheTest']);
Route::get('robin/pms-config', [RobinTest::class, 'pmsConfigCacheTest']);

//Route::get('robin/dbc', [RobinTest::class, 'databaseCache']);

Route::get('robin/time', [RobinTest::class, 'showServerTime']);

Route::get('robin/stats', [RobinTest::class, 'showDBStats']);
Route::get('robin/products', [RobinTest::class, 'products']);
Route::get('robin/product_offers', [RobinTest::class, 'product_offers']);
Route::get('robin/reports', [RobinTest::class, 'reports']);
