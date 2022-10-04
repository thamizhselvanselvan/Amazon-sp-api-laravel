<?php

namespace App\Http\Controllers\Inventory\Master\Rack;

use Illuminate\Http\Request;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Shelve;
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

            //     $data = Shelve::query()->rightJoin('racks as r', function($join) {
            //         $join->on("shelves.rack_id", "=", "r.id");
            //    })->orderBy('r.id')
            //    ->select('r.rack_id','r.id', 'r.name as rack_name', 'shelves.name')
            //    ;

            $data = Rack::with(['shelves']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('shelves_no', function ($data) use (&$cnt, &$rack_id) {
                    return (isset($data->shelves->first()->name)) ? $data->shelves->count() : 0;
                })
                ->editColumn('name', function ($data) {
                    return ($data->name) ? $data->name : "NA";
                })
                ->editColumn('shelve_name', function ($data) {
                    return (isset($data->shelves->first()->name)) ? $data->shelves->first()->name : "NA";
                })
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/racks/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<bu  tton data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</bu></div>';
                    return $actionBtn;
                })
                ->rawColumns(['name', 'shelves_no', 'shelve_name', 'action'])
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
        $warehouse_exists = Warehouse::where('id', $request->warehouse_id)->exists();

        if (!$warehouse_exists) {
            return redirect()->route('racks.create')->with('error', 'Selected  Warehouse is invalid');
        }

        $rack_lists = [];
        if (!$request->rack_id) {
            return redirect()->route('racks.create')->with('error', 'Enter Rack name and Rack ID And Click  Add');
        } else {
        foreach ($request->rack_id as $key => $rack_id) {

            $rack_lists[] = [
                'name' =>  $request->name[$key],
                'rack_id' =>  $rack_id,
                'warehouse_id' => $request->warehouse_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
    }
        Rack::insert($rack_lists);

        return redirect()->route('racks.index')->with('success', 'Racks  has been created successfully');
    }

    public function edit($id)
    {
        $warehouse_lists = Warehouse::get();
        $name = Rack::where('id', $id)->first();
        $selected_warehouse = $name->warehouse_id;
        return view('inventory.master.racks.rack.edit', compact('name', 'warehouse_lists', 'selected_warehouse'));
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'warehouse_id' => 'required',
            'name' => 'required|min:2|max:100',
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
