<?php

namespace App\Http\Controllers\Inventory\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryGlobalController extends Controller
{
    public function index()
    {
        return view('inventory.setting.system.index');
    }
}
