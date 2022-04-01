<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InventoryReportingController extends Controller
{
    public function Reportingindex()
    {
        return view('inventory.Reporting.Index');
    }
}