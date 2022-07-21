<?php

namespace App\Http\Controllers\BuisnessAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index()
    {
        return view('buisnessapi.orders.index');
        
    }
}
