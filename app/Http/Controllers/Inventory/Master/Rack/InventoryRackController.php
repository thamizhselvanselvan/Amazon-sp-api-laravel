<?php

namespace App\Http\Controllers\Inventory\Master\Rack;

use Illuminate\Http\Request;
use App\Models\Inventory\Rack;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

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
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/racks/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['shelves_count', 'action'])
                ->make(true);
        }

        return view('inventory.master.racks.rack.index');
    }

    public function create()
    {
        return view('inventory.master.racks.rack.add');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|min:3|max:100'
        ]);

        $name = $request->name;

        Rack::create(['name' => $name]);

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

