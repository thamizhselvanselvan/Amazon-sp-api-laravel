<?php

namespace App\Http\Controllers\BuisnessAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AWS_Business_API\AWS_POC\Orders;

class OrdersController extends Controller
{
    public function index()
    {
        $ApiCall = new Orders();
        $data = $ApiCall->getOrders();
        dd($data);
        return view('buisnessapi.orders.index');
    }
    
}
