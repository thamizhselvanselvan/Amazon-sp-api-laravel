<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Models\Inventory\Inventory;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse;

class ReportController extends Controller
{
    public function index(request $request) 
    {
        $ware_lists = Warehouse::get();
        return view('inventory.report.report', compact('ware_lists'));
    }
        
}
