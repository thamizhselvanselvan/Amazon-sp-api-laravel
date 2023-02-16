<?php

namespace App\Http\Controllers\V2\Masters;

use App\Http\Controllers\Controller;
use App\Models\V2\Masters\Currency;
use App\Models\V2\Masters\Region;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RegionController extends Controller
{
    //

    public function index(Request $request)
    {
        if ($request->isMethod('get')) {
            if ($request->ajax()) {
                $getData = ['id', 'region', 'region_code', 'url', 'site_url', 'marketplace_id', 'currency_id', 'status'];

                $data = Region::with(['currency'])->orderBy('id', 'DESC')->get($getData);
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('url', function ($row) {
                        $urls = "<div>";
                        $urls .= "<label>API Endpoint URL</label>";
                        $urls .= "<p>". $row->url ."</p>";  
                        $urls .= "</div>";
        
                        $urls .= "<div>";
                        $urls .= "<label>Site URL</label>";
                        $urls .= "<p>". $row->site_url ."</p>";  
                        $urls .= "</div>";
        
                        return $urls;
                    })
                    ->addColumn('action', function ($row) {
                        $actionBtn = '<div class="d-flex"><a href="/v2/master/store/regions/edit/' . $row['id'] . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                        $actionBtn .= '<button data-id="' . $row['id'] . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                        return $actionBtn;
                    })
                    ->editColumn('status', function ($row) {
                        return $row['status'] ? 'Active' : 'Inactive';

                    })
                    ->editColumn('currency', function ($row) {
                        return $row['currency']['name'];
                       
                    })
                    ->rawColumns(['action','url'])
                    ->make(true);
            }
            return view('v2.masters.store.regions.index');
        } else {
          
            $request->validate([
                'region_code' => 'required|alpha|min:2|max:35',
                'region' => 'required|regex:/^[\pL\s\-]+$/u|min:2|max:150',
                'marketplace_id' => 'required|alpha_num|min:14|max:35',
                'url' => 'required|url',
                'site_url' => 'required|url',
                

            ]);
            Region::create([
                'currency_id' => $request->currency_id,
                'region' => $request->region,
                'region_code' => $request->region_code,
                'url' => $request->url,
                'site_url' => $request->site_url,
                'marketplace_id' => $request->marketplace_id,
                'status' => $request->status,

            ]);

            return redirect()->intended('/v2/master/store/regions')->with('success', 'Region  has been added successfully');
        }
    }

    public function add()
    {
        $currencies = Currency::where('status', 1)->get();
        return view('v2.masters.store.regions.add',compact('currencies'));
    }

    public function edit($id)
    {
        $region = Region::find($id);
        $currencies = Currency::where('status', 1)->get();
        return view('v2.masters.store.regions.edit',compact('region','currencies'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'region_code' => 'required|alpha|min:2|max:35',
            'region' => 'required|regex:/^[\pL\s\-]+$/u|min:2|max:150',
            'marketplace_id' => 'required|alpha_num|min:14|max:35',
            'url' => 'required|url',
            'site_url' => 'required|url',
        ]);
        $records = [
            'region_code' => $request->region_code,
            'region' => $request->region,
            'marketplace_id' => $request->marketplace_id,
            'url' => $request->url,
            'site_url' => $request->site_url,
            'currency_id' => $request->currency_id,
            'status' => $request->status
        ];
        Region::where('id', $id)->update($records);
        return redirect()->intended('/v2/master/store/regions')->with('success', 'Region has been updated successfully');
    }

    public function delete(Request $request)
    {
        Region::find($request->id)->delete();
        return redirect()->intended('/v2/master/store/regions')->with('success', 'Region has been pushed to Bin successfully');
    }

    public function trashView(Request $request)
    {
        if ($request->ajax()) {
            $getData = ['id', 'region', 'region_code', 'url', 'site_url', 'marketplace_id', 'currency_id', 'status'];

            $region = Region::with(['currency'])->onlyTrashed()->get($getData);
            return DataTables::of($region)
                ->addIndexColumn()

                ->addColumn('action', function ($region) {
                    return '<button data-id="' . $region->id . '" class="restore btn btn-success"><i class="fas fa-trash-restore"></i> Restore</button>';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] ? 'Active' : 'Inactive';
                   
                })
                ->editColumn('currency', function ($row) {
                    return $row['currency']['name'];
                   
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('v2.masters.store.regions.trash-view');
    }

    public function restore(Request $request)
    {

        Region::where('id', $request->id)->restore();
        return response()->json(['success' => 'Region has restored successfully']);
    }
}
