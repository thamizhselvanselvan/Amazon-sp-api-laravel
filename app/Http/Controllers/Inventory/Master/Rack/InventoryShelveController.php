<?php

namespace App\Http\Controllers\Inventory\Master\Rack;

use Illuminate\Http\Request;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Shelve;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse;
use Yajra\DataTables\Facades\DataTables;

class InventoryShelveController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {


        // $data = Shelve::query()->with(['bins', 'racks', 'warehouses'])->get();
        // dd($data);

        if ($request->ajax()) {

            $data = Shelve::query()->with(['bins', 'racks', 'warehouses']);

            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('rack_name', function ($data) {
                //     return ($data->racks) ? $data->racks->name : "NA";
                // })
                ->addColumn('bins_count', function ($data) {
                    return ($data->bins) ? $data->bins->count() : 0;
                })
                ->addColumn('warehouse_name', function ($data) {
                    return ($data->warehouses) ? $data->warehouses->name : 'NA';
                })
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/shelves/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['rack_name', 'bins_count', 'warehouse_name', 'action'])
                ->make(true);
        }

        return view('inventory.master.racks.shelve.index');
    }

    public function create()
    {
        $rack_lists = Rack::get();
        $ware_lists = Warehouse::get();

        return view('inventory.master.racks.shelve.add', compact('rack_lists', 'ware_lists'));
    }

    public function store(Request $request)
    {

        // $request->validate([
        //     'name' => 'required|min:3|max:100',
        // ]);

        $warehouse_exists = Warehouse::where('id', $request->ware_id)->exists();

        if (!$warehouse_exists) {
            return redirect()->route('shelves.create')->with('error', 'Selected Warehouse is invalid');
        }
        $rack_exists = Rack::where('rack_id', $request->rack_id)->exists();

        if (!$rack_exists) {
            return redirect()->route('shelves.create')->with('error', 'Selected Rack is invalid');
        }
        if (!$request->shelve_id) {
            return redirect()->route('shelves.create')->with('error', 'Enter Shelve ID and Shelve Name And Click Add');
        }

        $shelve_lists = [];

        foreach ($request->shelve_id as $key => $shelve_id) {

            $shelve_lists[] = [
                'name' =>  $request->name[$key],
                'shelve_id' =>  $shelve_id,
                'rack_id' => $request->rack_id,
                'warehouse' => $request->ware_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Shelve::insert($shelve_lists);

        return redirect()->route('shelves.index')->with('success', 'Shelves has been created successfully');
    }

    public function edit($id)
    {
        $ware_lists = Warehouse::get();
        $shelve = Shelve::where('id', $id)->first();
        $rack_lists = Rack::get();
        $selected_warehouse = $shelve->warehouse;

        return view('inventory.master.racks.shelve.edit', compact(['shelve', 'rack_lists', 'ware_lists', 'selected_warehouse']));
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            // 'warehouse' => 'required',
            // 'rack_id' => 'required',
            'name' => 'required|min:3|max:100',
            'shelve_id' => 'required|min:1|max:100'


        ]);

        Shelve::where('id', $id)->update($validated);

        // $rack_exists = Rack::where('id', $request->rack_id)->exists();

        // if (!$rack_exists) {
        //     return redirect()->route('shelves.edit')->with('error', 'Selected Rack id invalid');
        // }
        // $warehouse_exists = Warehouse::where('name', $request->name)->exists();

        // if (!$warehouse_exists) {
        //     return redirect()->route('shelves.create')->with('error', 'Selected Warehouse is invalid');
        // }
        // Shelve::where('id', $id)->update([
        //     'shelve_id' => $request->shelve_id,
        //     'name' => $request->name,
        //     'warehouse' => $request->ware_id,
        //     'rack_id' => $request->rack_id
        // ]);

        return redirect()->route('shelves.index')->with('success', 'Shlves has been updated successfully');
    }

    public function destroy($id)
    {
        Shelve::where('id', $id)->delete();

        return redirect()->route('shelves.index')->with('success', 'Shelve has been Deleted successfully');
    }

    public function getRack($id)
    {
        $rack = Rack::where('warehouse_id', $id)->get();
        return response()->json($rack);
    }
}
