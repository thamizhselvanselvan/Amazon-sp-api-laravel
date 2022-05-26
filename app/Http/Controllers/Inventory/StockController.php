<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Models\Inventory\Inventory;
use App\Http\Controllers\Controller;
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
}
