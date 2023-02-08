<?php

namespace App\Http\Controllers\V2\Masters;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Company\CompanyMaster;
use App\Models\V2\Masters\Credential;
use App\Models\V2\Masters\Region;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class CredentialController extends Controller
{

    public function index(Request $request)
    {
        if ($request->isMethod('get')) {
            if ($request->ajax()) {
                $data = Credential::with(['region.currency', 'company'])
                    ->orderBy('id', 'DESC')
                    ->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $actionBtn = '<div class="d-flex"><a href="/v2/master/store/credentials/edit/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                        $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                        return $actionBtn;
                    })
                    ->editColumn('company', function ($row) {
                        return $row->company->company_name;
                    })
                    ->editColumn('region', function ($row) {
                      
                        return $row->region->marketplace_id.",".$row->region->currency->name.",".$row->region->region_code;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('v2.masters.store.credentials.index');
        }
        else {

            $request->validate([
                'company' => 'required',
                'store_name' => 'required|alpha_num|min:2|max:150',
                'seller_id' => 'required|alpha_num|max:35',
                'auth_code' => 'required|alpha_num|max:1000',
                'marketplace_id' => 'required',
                
            ]);
            Credential::create([
                'company_id' => $request->company,
                'store_name' => $request->store_name,
                'merchant_id' => $request->seller_id,
                'authcode' => $request->auth_code,
                'region_id' => $request->marketplace_id,
    
            ]);
            return redirect()->intended('/v2/master/store/credentials')->with('success', 'Credentials  has been created successfully');
        }

    }

    public function create()
    {
        $companys = CompanyMaster::select('id', 'company_name')->where('user_id', Auth::id())->get();
        $regions = Region::with(['currency'])->get();
        return view('v2.masters.store.credentials.add',compact('companys','regions'));
    }

    public function edit($id)
    {
        $companys = CompanyMaster::select('id', 'company_name')->where('user_id', Auth::id())->get();
        $regions = Region::with(['currency'])->get();
        $credential = Credential::find($id);
        return view('v2.masters.store.credentials.edit', compact('credential','companys','regions'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'company' => 'required',
            'store_name' => 'required|alpha_num|min:2|max:150',
            'seller_id' => 'required|alpha_num|max:35',
            'auth_code' => 'required|alpha_num|max:1000',
            'marketplace_id' => 'required',
            
        ]);

        $records = [
            'company_id' => $request->company,
            'store_name' => $request->store_name,
            'merchant_id' => $request->seller_id,
            'authcode' => $request->auth_code,
            'region_id' => $request->marketplace_id,
        ];
        Credential::where('id', $id)->update($records);
        return redirect()->intended('/v2/master/store/credentials')->with('success', 'Credential has been updated successfully');
    }

    public function delete(Request $request)
    {
        Credential::where('id', $request->id)->delete();

        return redirect()->intended('/v2/master/store/credentials')->with('success', 'Credential has been pushed to Bin successfully');
    }

    public function trashView(Request $request)
    {
        if ($request->ajax()) {
            $company =  Credential::with(['region.currency', 'company'])->onlyTrashed()->get();

            return DataTables::of($company)
                ->addIndexColumn()

                ->addColumn('action', function ($company) {
                    return '<button data-id="' . $company->id . '" class="restore btn btn-success"><i class="fas fa-trash-restore"></i> Restore</button>';
                })
                ->editColumn('company', function ($row) {
                    return $row->company->company_name;
                })
                ->editColumn('region', function ($row) {
                  
                    return $row->region->marketplace_id.",".$row->region->currency->name.",".$row->region->region_code;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('v2.masters.store.credentials.trash-view');
    }

    public function restore(Request $request)
    {

        Credential::where('id', $request->id)->restore();

        return response()->json(['success' => 'Credential has restored successfully']);
    }
}
