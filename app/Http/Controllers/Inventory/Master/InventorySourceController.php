<?php

namespace App\Http\Controllers\Inventory\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventorySourceController extends Controller
{
    public function index()
    {
        return view('inventory.master.source.index');
    }
}
