<?php

namespace App\Http\Controllers\V2\Masters;

use Illuminate\Http\Request;
use App\Models\V2\Masters\City;
use App\Models\V2\Masters\State;
use App\Models\V2\Masters\Country;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class GeoManagementController extends Controller
{
  public function index_country(Request $request)
  {
    if ($request->isMethod('get')) 
    {
      if ($request->ajax()) 
      {
        $data = Country::get();
        return DataTables::of($data)
          ->addIndexColumn()
          ->addColumn('action', function ($row) {
            $actionBtn = '<div class="d-flex"><a href="/v2/master/geo/country/edit/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
            $actionBtn .= '<div class="d-flex"><a href="/v2/master/geo/country/delete/' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
            return $actionBtn;
          })
          ->rawColumns(['action'])
          ->make(true);
      }
      return view('v2.masters.geo.country.index');
    } 
    else
     {
      $geo_data = $request->validate(
        [
          'name' => 'required|unique:App\Models\V2\Masters\Country',
          'country_code' => 'required|unique:App\Models\V2\Masters\Country',
          'code' => 'required|unique:App\Models\V2\Masters\Country',
          'numeric_code' => 'required|unique:App\Models\V2\Masters\Country',
          'phone_code' => 'required|unique:App\Models\V2\Masters\Country',
          'capital' => 'required|unique:App\Models\V2\Masters\Country',
          'currency' => 'required|unique:App\Models\V2\Masters\Country',
          'currency_name' => 'required|unique:App\Models\V2\Masters\Country',
          'currency_symbol' => 'required|unique:App\Models\V2\Masters\Country',

        ]
      );
      $country_name = $request->name;
      Country::insert($geo_data);
      return redirect('v2/master/geo/country')->with('message', $country_name . ' Added');
    }
  }


  public function add_country()
  {
    return view('v2.masters.geo.country.add');
  }

  public function add_state()
  {
    $countries = Country::all();
    return view('v2.masters.geo.state.add', compact('countries'));
  }

  public function add_city()
  {
    $countries = Country::all();
    $states = State::all();
    return view('v2.masters.geo.city.add', compact('states'),compact('countries'));
  }

  public function index_state(Request $request)
  {
    
    if ($request->isMethod('get')) 
    {
      $data = State::with(['country']);
      if ($request->ajax()) 
      {

        return Datatables::of($data)
          ->addIndexColumn()
          ->editColumn('country_name', function ($data) {
            return ($data->id) ? $data->country->name : "NA";
          })
          ->addColumn('action', function ($row) {
            $actionBtn = '<div class="d-flex"><a href="/v2/master/geo/state/edit/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
            $actionBtn .= '<div class="d-flex"><a href="/v2/master/geo/state/delete/' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
            return $actionBtn;
          })
          ->rawColumns(['country_name', 'action'])
          ->make(true);
      }
      return view('v2.masters.geo.state.index');
    } 
    else
     {
      $request->validate(
        [
          'country_id' => 'required',
          'name' => 'unique:App\Models\V2\Masters\State',
        ]
      );
      $state = new State;
      $state->country_id = $request->get('country_id');
      $state->name = $request->get('name');
      $state->save();
      return redirect('v2/master/geo/state')->with('message',  $state->name . ' Added');
    }
  }

  public function getStates(Request $request)
  {
    // dd($request->cid);
    $state = State::select('id', 'name')->where('country_id', $request->cid)->get()->toArray();
    echo json_encode($state);
    

  }

  public function index_city(Request $request)
  {

    if ($request->isMethod('get')) 
    {

      if ($request->ajax()) 
      {

        $data = City::with(['states']);
        return Datatables::of($data)
          ->addIndexColumn()
          ->editColumn('state_name', function ($data) {

            $city = isset($data->states['name']) ? $data->states['name'] : "NA";
            return $city;
          })
          ->addColumn('action', function ($row) {
            $actionBtn = '<div class="d-flex"><a href="/v2/master/geo/city/edit/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
            $actionBtn .= '<div class="d-flex"><a href="/v2/master/geo/city/delete/' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
            return $actionBtn;
          })
          ->rawColumns(['state_name', 'action'])
          ->make(true);
      }
      return view('v2.masters.geo.city.index');
    } 
    
    else 
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
      return redirect('v2/master/geo/city')->with('message', $city->name . ' Added');
    }
  }


  // public function store_country(Request $request)
  // {
  //   $geo_data = $request->validate(
  //     [
  //       'name' => 'required|unique:App\Models\V2\Masters\Country',
  //       'country_code' => 'required|unique:App\Models\V2\Masters\Country',
  //       'code' => 'required|unique:App\Models\V2\Masters\Country',
  //       'numeric_code' => 'required|unique:App\Models\V2\Masters\Country',
  //       'phone_code' => 'required|unique:App\Models\V2\Masters\Country',
  //       'capital' => 'required|unique:App\Models\V2\Masters\Country',
  //       'currency' => 'required|unique:App\Models\V2\Masters\Country',
  //       'currency_name' => 'required|unique:App\Models\V2\Masters\Country',
  //       'currency_symbol' => 'required|unique:App\Models\V2\Masters\Country',

  //     ]
  //   );
  //   $country_name = $request->name;
  //   Country::insert($geo_data);
  //   return redirect('v2/master/geo/country')->with('message', $country_name . ' Added');
  // }

  // public function store_state(Request $request)
  // {
  //   $request->validate(
  //     [
  //       'country_id' => 'required',
  //       'name' => 'unique:App\Models\V2\Masters\State',
  //     ]
  //   );
  //   $state = new State;
  //   $state->country_id = $request->get('country_id');
  //   $state->name = $request->get('name');
  //   $state->save();
  //   return redirect('v2/master/geo/state')->with('message',  $state->name . ' Added');
  // }

  // public function store_city(Request $request)
  // {
  //   $request->validate(
  //     [
  //       'state_id' => 'required',
  //       'name' => 'required',
  //     ]
  //   );
  //   $city = new City;
  //   $city->state_id = $request->get('state_id');
  //   $city->name = $request->get('name');
  //   $city->save();
  //   return redirect('v2/master/geo/city')->with('message', $city->name . ' Added');
  // }


  public function destroy_country(Country $country, $country_id)
  {
    $country = Country::where('id', $country_id)->first();
    $country_name = $country->name;
    if (!$country) {
      // Country code doesn't exists
      return false;
    }

    $state_ids  = State::select('id')->where('country_id', $country_id)->get()->pluck('id')->toArray();
    $city_ids = [];


    foreach ($state_ids as $state_id) {

      $city_ids = City::select('id')->where('state_id', $state_id)->get()->pluck('id')->toArray();
      if (count($city_ids) != 0) {
        City::whereIn('id', $city_ids)->delete();
      }
    }

    if (count($state_ids) != 0) {
      State::where('country_id', $country_id)->delete();
    }

    $country->delete();

    return redirect('/v2/master/geo/country')->with('danger', $country_name . ' has deleted Successfully');
  }

  public function destroy_state(State $state, $id)
  {
    $state = State::find($id);
    $state->delete();
    $city = City::where('state_id', $id)->delete();
    return redirect('v2/master/geo/state')->with('danger', $state->name  .  ' has deleted Successfully');
  }

  public function destroy_city(City $city, $id)
  {
    $city = City::find($id);
    $city->delete();
    return redirect('/v2/master/geo/city')->with('danger', $city->name  . ' has deleted Successfully');
  }

  public function edit_country(Country $country, $id)
  {
    $countries = Country::find($id);
    return view('v2.masters.geo.country.edit', ['countries' => $countries]);
  }

  public function edit_state(State $state, $id)
  {
    $countries = Country::get();
    $states = State::find($id);
    return view('v2.masters.geo.state.edit', compact('countries', 'states'));
  }

  public function edit_city(City $city, $id)
  {
    $states = State::get();
    $cities = City::find($id);
    return view('v2.masters.geo.city.edit', compact('states', 'cities'));
  }


  public function update_country(Request $request, Country $country, $id)
  {
    $geo_data = $request->validate(
      [
        'name' => 'required',
        'country_code' => 'required',
        'code' => 'required',
        'numeric_code' => 'required',
        'phone_code' => 'required',
        'capital' => 'required',
        'currency' => 'required',
        'currency_name' => 'required',
        'currency_symbol' => 'required',
      ]
    );
    $country_name = $request->name;
    Country::find($id)->update($geo_data);
    return redirect('v2/master/geo/country')->with('message', $country_name .  ' has updated Successfully');
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
    return redirect('v2/master/geo/state')->with('message', $states->name .  ' has updated Successfully');
  }

  public function update_city(Request $request, City $city, $id)
  {
    $request->validate(
      [
        'state' => 'required',
        'city_name' => 'required',
      ]
    );
    $cities = City::find($id);
    $cities->state_id = $request->get('state');
    $cities->name = $request->get('city_name');
    $cities->save();
    return redirect('v2/master/geo/city')->with('message', $cities->name .  ' has updated Successfully');
  }
}
