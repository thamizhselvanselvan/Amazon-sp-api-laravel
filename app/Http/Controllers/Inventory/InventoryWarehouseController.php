<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class InventoryWarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Warehouse::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/warehouses/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('inventory.warehouse.index');
    }
    public function create()
    {
        return view('inventory.warehouse.add');
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|min:3|max:100',
            'name' =>'required|min:3|max:100',
            'address_1'=>'required|min:3|max:100',
            'address_2'=>'required|min:3|max:100',
            'city'=>'required|min:3|max:100',
            'state'=>'required|min:3|max:100',
            'country'=>'required|min:3|max:100',
            'pin_code'=>'required|min:3|max:100',
            'contact_person_name'=>'required|min:3|max:100',
            'phone_number' =>'required|min:3|max:100',
            'email'=>'required|min:3|max:100',
        ]);

        Warehouse::create([
            'name' => $request->name,
            'address_1' => $request->address_1,
            'address_2' => $request->address_2,
            'city' => $request->city,
            'state'=>$request->state,
            'country'=>$request->country,
            'pin_code' => $request->pin_code,
            'contact_person_name'=> $request->contact_person_name,
            'phone_number' =>$request->phone_number,
            'email' =>$request->email,

        ]);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse ' . $request->name . ' has been created successfully');
    }
    public function edit($id)
    {

        $name = Warehouse::where('id', $id)->first();

        return view('inventory.warehouse.edit', compact('name'));
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|min:3|max:100',
            'name' =>'required|min:3|max:100',
            'address_1'=>'required|min:3|max:100',
            'address_2'=>'required|min:3|max:100',
            'city'=>'required|min:3|max:100',
            'state'=>'required|min:3|max:100',
            'country'=>'required|min:3|max:100',
            'pin_code'=>'required|min:3|max:100',
            'contact_person_name'=>'required|min:3|max:100',
            'phone_number' =>'required|min:3|max:100',
            'email'=>'required|min:3|max:100',
        ]);

        Warehouse::where('id', $id)->update($validated);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse has been updated successfully');
    }

    public function destroy($id)
    {
        Warehouse::where('id', $id)->delete();

        return redirect()->route('warehouses.index')->with('success', 'Warehouse has been Deleted successfully');
    }
}
