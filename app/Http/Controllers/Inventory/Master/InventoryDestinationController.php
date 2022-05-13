<?php

namespace App\Http\Controllers\Inventory\Master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Destination;
use Yajra\DataTables\Facades\DataTables;

class InventoryDestinationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Destination::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/destinations/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('inventory.master.destination.index');
    }
    public function create()
    {
        return view('inventory.master.destination.add');
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|min:3|max:100',
        ]);

        Destination::create([
            'name' => $request->name,
        ]);

        return redirect()->route('destinations.index')->with('success', 'destination ' . $request->name . ' has been created successfully');
    }
    public function edit($id)
    {

        $name = Destination::where('id', $id)->first();

        return view('inventory.master.destination.edit', compact('name'));
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|min:3|max:100',
        ]);

        Destination::where('id', $id)->update($validated);

        return redirect()->route('destinations.index')->with('success', 'Destination has been updated successfully');
    }

    public function destroy($id)
    {
        Destination::where('id', $id)->delete();

        return redirect()->route('destinations.index')->with('success', 'Destinations has been Deleted successfully');
    }
}