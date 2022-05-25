<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Models\Inventory\Vendor;
use App\Models\Inventory\City;
use App\Models\Inventory\State;
use App\Models\Inventory\Country;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class InventoryVendorController extends Controller
{
    public function index(Request $request)
    {
         if ($request->ajax()) {

            $data = Vendor::query()->with(['countrys','states', 'citys']);

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('country_name', function ($data) {
                    return ($data->countrys) ? $data->countrys->name : "NA";
                })

                ->editColumn('state_name', function ($data) {
                    return ($data->states) ? $data->states->name : "NA";
                })
                ->editColumn('city_name', function ($data) {
                    return ($data->citys) ? $data->citys->name : "NA";
                })

                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/vendors/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['action','country_name','state_name','city_name'])
                ->make(true);
        }
        return view('inventory.vendor.index');
    }
    public function create()
    {
        $country = Country::select('id','name')->get();
        return view('inventory.vendor.add',compact('country'));
    }
    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|min:3|max:100',
            'type' => 'required|in:Source,Destination',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'currency' => 'required',
        ]);

        Vendor::create([
            'name' => $request->name,
            'type' => $request->type,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'currency' => $request->currency,
        ]);

        return redirect()->route('vendors.index')->with('success', 'Vendor ' . $request->name . ' has been created successfully');
    }
    
    public function edit($id)
    {

        $name = Vendor::where('id', $id)->first();
        $country = Country::select('id','name')->get();

        return view('inventory.vendor.edit', compact('name','country'));
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|min:3|max:100',
            'type' => 'required|in:Source,Destination',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
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

    public function getState(Request $request, $id)
    {
        if($request->ajax())
        {
            $state = State::where('country_id',$id)->get();
        }
        return response()->json($state);
    }
    
    public function getCity(Request $request, $id)
    {
        if($request->ajax())
        {
            $city = City::where('state_id',$id)->get();
        }
        return response()->json($city);
    }
}
