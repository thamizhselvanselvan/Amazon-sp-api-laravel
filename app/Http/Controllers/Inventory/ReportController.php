<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Models\Inventory\Inventory;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index(request $request) 
    {
        $ware_lists = Inventory::with(['warehouses'])->get()->unique('warehouse_id');



        if ($request->ajax()) {
            $ware = Inventory::query()
            ->select('inventory.*', 'warehouses.name')
            ->join('warehouses', function($query) {
                $query->on("warehouses.id", "=", "inventory.warehouse_id");
            })->where('warehouse_id', $request->id)->get();

            return response()->json($ware);
        }

        return view('inventory.report.report', compact('ware_lists'));
    }
        
}
