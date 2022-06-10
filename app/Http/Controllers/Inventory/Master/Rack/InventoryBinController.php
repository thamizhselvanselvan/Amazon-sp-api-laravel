<?php

namespace App\Http\Controllers\Inventory\Master\Rack;

use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Shelve;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class InventoryBinController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Bin::query()->withCount('shelves');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('shelves_count', function ($data) {
                    return $data->shelves_count;
                })
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/bins/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';

                    return $actionBtn;
                })
                ->rawColumns(['shelves_count', 'action'])
                ->make(true);
        }

        return view('inventory.master.racks.bin.index');
    }

    public function create(Request $request, $rack_id = null, $shelve_id = null)
    {
        $rack_lists = Rack::get();
        $shelves = Shelve::query();
        $ware_lists = Warehouse::get();
        $shelve_lists = ($rack_id) ? $shelves->where('rack_id', $rack_id)->get() : [];

        $rack_id = $rack_id ? $rack_id : '';
        $shelve_id = $shelve_id ? $shelve_id : '';

        return view('inventory.master.racks.bin.add', compact('rack_lists', 'shelve_lists', 'rack_id', 'shelve_id', 'ware_lists'));
    }

    public function rackselect($id)
    {

        $name = Rack::where('id', $id)->first();

        return view('inventory.master.racks.bin.edit', compact('name'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'ware_id' => 'required',
            'rack_id' => 'required',
            'shelve_id' => 'required',
            'name' => 'required',
            'depth' => 'required',
            'width' => 'required',
            'height' => 'required',

        ]);

        $shelve_exists = Shelve::where('shelve_id', $request->shelve_id)->exists();

        if (!$shelve_exists) {
            return redirect()->route('bins.create')->with('error', 'Selected shelve id invalid');
        }

        $bin_lists = [];

        foreach ($request->bin_id as $key => $bin_id) {

            $bin_lists[] = [
                'bin_id' => $bin_id,
                'name' =>  $request->name[$key],
                'width' =>  $request->width[$key],
                'height' =>  $request->height[$key],
                'depth' =>  $request->depth[$key],
                'warehouse' => $request->ware_id,
                'shelve_id' => $request->shelve_id,
                'rack_id' => $request->rack_id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        Bin::insert($bin_lists);

        return redirect()->route('bins.index')->with('success', 'Bin  has been created successfully');
    }

    public function edit($id)
    {

        $name = Bin::where('id', $id)->first();

        return view('inventory.master.racks.bin.edit', compact('name'));
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([
           
            // 'ware_id' => 'required',
            // 'rack_id' => 'required',
            // 'shelve_id' => 'required',
            'bin_id' =>'required',
            'name' => 'required',
            'depth' => 'required',
            'width' => 'required',
            'height' => 'required',

        ]);

        Bin::where('id', $id)->update($validated);

        return redirect()->route('bins.index')->with('success', 'Bin has been updated successfully');
    }

    public function destroy($id)
    {
        Bin::where('id', $id)->delete();

        return redirect()->route('bins.index')->with('success', 'Bin has been Deleted successfully');
    }

    public function getBinRack($id)
    {
        $binRack = Rack::where('warehouse_id', $id)->get();
        return response()->json($binRack);
    }

    public function getBinRackShelve($id)
    {
        $binShelve = Shelve::where('rack_id', $id)->get();
        return response()->json($binShelve);
    }
}
