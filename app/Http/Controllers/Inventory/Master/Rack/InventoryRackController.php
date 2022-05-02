<?php

namespace App\Http\Controllers\Inventory\Master\Rack;

use Illuminate\Http\Request;
use App\Models\Inventory\Rack;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Warehouse;

class InventoryRackController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Rack::query()->with(['shelves']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('shelves_count', function ($data) {
                    return ($data->shelves) ? $data->shelves->count() : 0;
                })

                ->addColumn('shelve_name', function ($data) {
                    return ($data->shelves->first()) ? $data->shelves->first()->name : "NA";
                })
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/racks/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<bu  tton data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</bu></div>';
                    return $actionBtn;
                })
                ->rawColumns(['shelves_count', 'shelve_name', 'action'])
                ->make(true);
        }


        return view('inventory.master.racks.rack.index');
    }


    public function create()
    {
        $warehouse_lists = Warehouse::get();
        return view('inventory.master.racks.rack.add', compact('warehouse_lists'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|min:3|max:100',
            'rack_id' => 'required|min:1|max:100'
        ]);
        $warehouse_exists = Warehouse::where('id', $request->warehouse_id)->exists();

        if (!$warehouse_exists) {
            return redirect()->route('racks.create')->with('error', 'Selected Warehouse is invalid');
        }

        $name = $request->name;
        $rack_id = $request->rack_id;
        $warehouse_id = $request->warehouse_id;
        Rack::create(['name' => $name, 'rack_id' => $rack_id, 'warehouse_id' => $warehouse_id]);

        return redirect()->route('racks.index')->with('success', 'Racks ' . $name . ' has been created successfully');
    }

    public function edit($id)
    {

        $name = Rack::where('id', $id)->first();

        return view('inventory.master.racks.rack.edit', compact('name'));
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|min:3|max:100',
            'rack_id' => 'required|min:1|max:100'
        ]);

        Rack::where('id', $id)->update($validated);

        return redirect()->route('racks.index')->with('success', 'Rack has been updated successfully');
    }

    public function destroy($id)
    {
        Rack::where('id', $id)->delete();

        return redirect()->route('racks.index')->with('success', 'Rack has been Deleted successfully');
    }
}
