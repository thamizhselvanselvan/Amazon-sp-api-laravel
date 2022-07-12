<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::get('amazon/invoice', 'AmazonInvoice\AmazonInvoiceManagementController@index');