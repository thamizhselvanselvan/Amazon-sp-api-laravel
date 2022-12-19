<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use League\Csv\Reader;
use App\Models\Inventory\City;
use App\Models\Inventory\State;
use App\Models\Inventory\Warehouse;
use App\Models\Inventory\Country;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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
            
            $data = Warehouse::query()->with(['countrys','states', 'citys']);
            

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

                    $actionBtn = '<div class="d-flex"><a href="/inventory/warehouses/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['action','country_name','state_name','city_name'])
                ->make(true);
        }

        return view('inventory.warehouse.index');
    }
    public function create()
    {
        $country =Country::select('id','name')->get();

        return view('inventory.warehouse.add',compact('country'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
          
            'name' =>'required|min:3|max:100',
            'address_1'=>'required|min:3|max:100',
            'address_2'=>'required|min:3|max:100',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
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
        $country =Country::select('id','name')->get();
        $name = Warehouse::where('id', $id)->first();
        $country = Country::get();
       
        $selected_country = $name->country;
        $selected_state = $name->state;
        $selected_city = $name->city;
       
        
        return view('inventory.warehouse.edit', compact('name','country','selected_country','selected_state','selected_city'));
    }

    public function update(Request $request, $id)
    {

        $validated = $request->validate([
           
            'name' =>'required|min:3|max:100',
            'address_1'=>'required|min:3|max:100',
            'address_2'=>'required|min:3|max:100',
            'city'=>'required',
            'state'=>'required',
            'country'=>'required',
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

    public function CountryStateCity(Request $request,$id)
    {
        if($request->ajax())
        {
            $statename = State::where('country_id', $id)->get();
        }
        return response()->json($statename);
    }

    public function getState(Request $request, $id)
    {
        if($request->ajax())
        {
            $city=City::where('state_id',$id)->get();
        }
        return response()->json($city);
        
    }
}
