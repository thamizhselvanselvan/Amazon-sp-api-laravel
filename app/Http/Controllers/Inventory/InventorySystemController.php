<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InventorySystemController extends Controller
{
    public function SystemIndex()
    {
        return view('Inventory.System.Index');
    }
}