<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Models\Inventory\Vendor;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class InventoryVendorController extends Controller
{
    public function index(Request $request)
    {
         if ($request->ajax()) {

            $data = Vendor::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/vendors/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('inventory.vendor.index');
    }
    public function create()
    {
        return view('inventory.vendor.add');
    }
    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|min:3|max:100',
            'type' => 'required|in:Source,Destination',
            'country' => 'required|min:1|max:100',
            'currency' => 'required|min:1|max:10',
        ]);

        Vendor::create([
            'name' => $request->name,
            'type' => $request->type,
            'country' => $request->country,
            'currency' => $request->currency,
        ]);

        return redirect()->route('vendors.index')->with('success', 'Vendor ' . $request->name . ' has been created successfully');
    }
    
    public function edit($id)
    {

        $name = Vendor::where('id', $id)->first();

        return view('inventory.vendor.edit', compact('name'));
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|min:3|max:100',
            'type' => 'required|in:Source,Destination',
            'country' => 'required|min:1|max:100',
            'currency' => 'required|min:1|max:10',
        ]);

        Vendor::where('id', $id)->update($validated);

        return redirect()->route('vendors.index')->with('success', 'Vendor has been updated successfully');
    }

    public function destroy($id)
    {
        Vendor::where('id', $id)->delete();

        return redirect()->route('vendors.index')->with('success', 'Vendor has been Deleted successfully');
    }
}
