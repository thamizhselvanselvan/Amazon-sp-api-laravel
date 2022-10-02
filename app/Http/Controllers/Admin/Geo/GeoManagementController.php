<?php

namespace App\Http\Controllers\Admin\Geo;

use Illuminate\Http\Request;
use App\Models\Inventory\City;
use App\Models\Inventory\State;
use Illuminate\Validation\Rule;
use App\Models\Inventory\Country;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

class GeoManagementController extends Controller
{
  public function index_country(Request $request)
  {

    if ($request->ajax()) {
      $data = Country::get();

      return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function ($row) {
          $actionBtn = '<div class="d-flex"><a href="/edit_country/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
          $actionBtn .= '<div class="d-flex"><a href="/delete_country/' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
          return $actionBtn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    return view('admin.geoManagement.Country.index');
  }
  //$countries = Country::all();
  // $countries = Country::paginate(10);
  //$countries = Country::paginate(15)->withQueryString();
  //$states = State::all();
  //return view('admin.geoManagement.Country.geo', compact('countries','states'));

  public function add_country()
  {
    return view('admin.geoManagement.Country.add');
  }

  public function add_state()
  {
    $countries = Country::all();
    return view('admin.geoManagement.State.add', compact('countries'));
  }

  public function add_city()
  {
    $states = State::all();
    return view('admin.geoManagement.City.add', compact('states'));
  }

  public function index_state(Request $request)
  {
    // $data = State::with(['country']);
    // dd($data);
    $data = State::with(['country']);
    if ($request->ajax()) {

      return Datatables::of($data)
        ->addIndexColumn()
        ->editColumn('country_name', function ($data) {
          return ($data->id) ? $data->country->name : "NA";
      })
        ->addColumn('action', function ($row) {
          $actionBtn = '<div class="d-flex"><a href="/edit_state/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
          $actionBtn .= '<div class="d-flex"><a href="/delete_state/' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
          return $actionBtn;
        })
        ->rawColumns(['country_name','action'])
        ->make(true);
    }
    return view('admin.geoManagement.State.index');
  }

  public function index_city(Request $request)
  {
    // $cities = City::all();
    // $cities = City::paginate(10);

    
    // exit;
    if ($request->ajax()) {

      $data = City::with(['states']);
      return Datatables::of($data)
        ->addIndexColumn()
        ->editColumn('state_name', function ($data) {
          
          $city = isset($data->states['name']) ? $data->states['name'] : "NA";
        return $city;
          
      })
        ->addColumn('action', function ($row) {
          $actionBtn = '<div class="d-flex"><a href="/edit_city/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
          $actionBtn .= '<div class="d-flex"><a href="/delete_city/' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
          return $actionBtn;
        })
        ->rawColumns(['state_name','action'])
        ->make(true);
  }
    return view('admin.geoManagement.City.index');
  }


  public function store_country(Request $request)
  {
    $geo_data = $request->validate(
      [
        'name' => 'required|unique:App\Models\Inventory\Country',
        'country_code' => 'required|unique:App\Models\Inventory\Country',
        'code' => 'required|unique:App\Models\Inventory\Country',
        'numeric_code' => 'required|unique:App\Models\Inventory\Country',
        'phone_code' => 'required|unique:App\Models\Inventory\Country',
        'capital' => 'required|unique:App\Models\Inventory\Country',
        'currency' => 'required|unique:App\Models\Inventory\Country',
        'currency_name' => 'required|unique:App\Models\Inventory\Country',
        'currency_symbol' => 'required|unique:App\Models\Inventory\Country',

      ]
    );
$country_name = $request->name;
    Country::insert($geo_data);
    return redirect('show_country')->with('message', $country_name . ' Added');
  }

  public function store_state(Request $request)
  {
    $request->validate(
      [
        'country_id' => 'required',
        'name' => 'unique:App\Models\Inventory\State',
      ]
    );
    $state=new State;
    $state->country_id=$request->get('country_id');
    $state->name=$request->get('name');
    $state->save();
    return redirect('show_state')->with('message',  $state->name . ' Added');
  }

  public function store_city(Request $request)
  {
    $request->validate(
      [
        'state_id' => 'required',
        'name' => 'required',
      ]
    );
    $city = new City;
    $city->state_id = $request->get('state_id');
    $city->name = $request->get('name');
    $city->save();
    return redirect('show_city')->with('message', $city->name . ' Added');
  }

  public function show_country(Country $country)
  {
    $countries = Country::all();
    return view('admin.geoManagement.Country.index', compact('countries'));
  }

  public function show_state(Country $country, State $state)
  {
    $countries = Country::all();
    $states = State::all();
    return view('admin.geoManagement.State.index', compact('countries', 'states'));
  }

  public function show_city(State $state, City $city)
  {
    $states = State::all();
    $cities = City::all();
    return view('admin.geoManagement.City.index', compact('cities', 'states'));
  }

  public function destroy_country(Country $country, $country_id)
  {
    $country = Country::where('id', $country_id)->first();
    $country_name = $country->name;
    if(!$country) {
      // Country code doesn't exists
      return false;
    }

    $state_ids  = State::select('id')->where('country_id', $country_id)->get()->pluck('id')->toArray();
    $city_ids = [];

    if(count($state_ids) == 0) {
      // No state Found
      return false;
    }
    
    foreach($state_ids as $state_id)
    {

      $city_ids = City::select('id')->where('state_id', $state_id)->get()->pluck('id')->toArray();
      City::whereIn('id', $city_ids)->delete();
      
    }

 
    State::where('country_id', $country_id)->delete();
    $country->delete();

    return redirect('show_country')->with('danger', $country_name . ' has deleted Successfully');
  }
  
  public function destroy_state(State $state, $id)
  {
    $state = State::find($id);
    $state->delete();
    $city=City::where('state_id',$id)->delete();
    return redirect('show_state')->with('danger',$state->name  .  ' has deleted Successfully');
  }

  public function destroy_city(City $city, $id)
  {
    $city = City::find($id);
    $city->delete();
    return redirect('show_city')->with('danger',$city->name  . ' has deleted Successfully');
  }

  public function edit_country(Country $country, $id)
  {
    $countries = Country::find($id);
    return view('admin.geoManagement.Country.edit', ['countries' => $countries]);
  }

  public function edit_state(State $state, $id)
  {
    $countries = Country::get();
    // dd($countries );
    $states = State::find($id);
    return view('admin.geoManagement.State.edit', compact('countries', 'states'));
  }

  public function edit_city(City $city, $id)
  {
    $states = State::get();
    $cities = City::find($id);
    return view('admin.geoManagement.City.edit', compact('states', 'cities'));
  }


  public function update_country(Request $request, Country $country, $id)
  {
    $geo_data = $request->validate(
      [
        'name' => 'required:App\Models\Inventory\Country',
        'country_code' => 'required:App\Models\Inventory\Country',
        'code' => 'required:App\Models\Inventory\Country',
        'numeric_code' => 'required:App\Models\Inventory\Country',
        'phone_code' => 'required:App\Models\Inventory\Country',
        'capital' => 'required:App\Models\Inventory\Country',
        'currency' => 'required:App\Models\Inventory\Country',
        'currency_name' => 'required:App\Models\Inventory\Country',
        'currency_symbol' => 'required:App\Models\Inventory\Country',
      ]
    );
    $country_name = $request->name;
    Country::find($id)->update($geo_data);
    return redirect('show_country')->with('message', $country_name .  ' has updated Successfully');
  }

  public function update_state(Request $request, State $state, $id)
  {
    $request->validate(
      [
        'country' => 'required',
        'state_name' => 'required',
      ]
    );
    $states = State::find($id);
    $states->country_id = $request->get('country');
    $states->name = $request->get('state_name');
    $states->save();
    return redirect('show_state')->with('message', $states->name.  ' has updated Successfully');
  }

  public function update_city(Request $request, City $city, $id)
  {
    $request->validate(
      [ 'state' => 'required',
        'city_name' => 'required',
      ]
    );
    $cities = City::find($id);
    $cities->state_id = $request->get('state');
    $cities->name = $request->get('city_name');
    $cities->save();
    return redirect('show_city')->with('message', $cities->name.  ' has updated Successfully');
  }
}
