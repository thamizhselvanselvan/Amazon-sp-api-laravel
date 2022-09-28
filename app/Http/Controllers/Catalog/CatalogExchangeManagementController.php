<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CatalogExchangeManagementController extends Controller
{
    public function index()
    {
        return view('Catalog.ExchangeRate.index');
    }
    
}
