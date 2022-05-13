<?php

namespace App\Http\Controllers\Inventory\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryInwardingController extends Controller
{
  function index(){
      return view('inventory.stock.inwarding.index');
  }
}
