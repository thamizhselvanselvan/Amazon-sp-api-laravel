<?php

use App\Http\Controllers\PMSPHPUnitTestController;

Route::get('pms/phpunit/api-get', [PMSPHPUnitTestController::class, 'phpunit_api_get']);
Route::get('pms/phpunit/mailgun-api-get', [PMSPHPUnitTestController::class, 'phpunit_mail_gun_api_get']);

Route::post('pms/phpunit/api-post', [PMSPHPUnitTestController::class, 'phpunit_api_post']);
Route::post('pms/phpunit/mailgun-api-post', [PMSPHPUnitTestController::class, 'phpunit_mail_gun_api_post']);

