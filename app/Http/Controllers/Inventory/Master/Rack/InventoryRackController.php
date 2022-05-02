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
        // $data = Shelve::query()->with(['racks']);
        // dd($data);
        if ($request->ajax()) {

            $data = Shelve::query()->join('racks as r', function($join) {
                $join->on("r.id", "=", "shelves.rack_id");
           })->orderBy('r.id')
           ->select('r.rack_id','r.id', 'r.name as rack_name', 'shelves.name')
           ;
            $cnt = 1;
            $rack_id = 0;
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('shelves_no', function ($data) use (&$cnt, &$rack_id) {
                    
                    if($rack_id != $data->id) {
                        $rack_id = $data->id;
                        $cnt = 1;
                    }

                    return $cnt++;
                })
                ->addColumn('rack_name', function ($data) {
                    return ($data->rack_name) ? $data->rack_name : "NA";
                })
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/racks/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<bu  tton data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</bu></div>';
                    return $actionBtn;
                })
                ->rawColumns([ 'shelves_no','rack_name', 'action'])
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
