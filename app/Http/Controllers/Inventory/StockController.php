<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Shipment;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{

    public function stokes(Request $request)
    {

        if ($request->ajax()) {

            $data = Inventory::query()->with(['warehouses']);

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('warehouse_name', function ($data) {
                    return ($data->warehouses) ? $data->warehouses->name : "NA";
                })
                ->rawColumns(['warehouse_name'])
                ->make(true);
        }

        return view('inventory.stock.view');
    }

    public function dashboard()
    {
        $ware_lists = Inventory::with(['warehouses'])->get();

        return view('inventory.stock.dashboard', compact('ware_lists'));
    }

    public function getlist(Request $request)
    {
        if ($request->ajax()) {
            $ware = Inventory::query()
            ->select('inventory.*', 'warehouses.name')
            ->join('warehouses', function($query) {
                $query->on("warehouses.id", "=", "inventory.warehouse_id");
            })->where('warehouse_id', $request->id)->get();

            return response()->json($ware);
        }
    }
}
