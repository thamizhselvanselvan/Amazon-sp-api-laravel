<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InventoryMasterController extends Controller
{
    public function IndexView()
    {
        return view('Inventory.Master.Index');
    }
}