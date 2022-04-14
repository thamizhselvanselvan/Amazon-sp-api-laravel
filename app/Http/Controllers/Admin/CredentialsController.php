<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aws_credential;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CredentialsController extends Controller
{
    
    public function index(Request $request)
    { 
        
        // dd($data[0]->mws_region->currency->name);

        if($request->ajax()){

            $data = Aws_credential::with(['mws_region'])->orderby('sync_status', 'DESC')->get();
        
            return DataTables::of($data)
                ->addIndexcolumn()
                ->editColumn('region', function ($row){
                    return ($row->mws_region->region)." [".($row->mws_region->region_code)."]" ;
                })
                ->addColumn('marketplace_id', function ($row){
                    return ($row->mws_region->marketplace_id) ;
                })
                ->addColumn('currency_name', function ($row){
                    return (($row->mws_region->currency->name)." [".($row->mws_region->currency->code).']') ;
                })
                ->editColumn('api_type', function ($row){
                    return ($row->api_type) ? 'SP API' : 'MWS';
                })
                ->editColumn('verified', function ($row){
                    return ($row->verified) ? 'Verified' : 'Unverified';
                })
                ->editColumn('status', function ($row){
                    return ($row->status)? 'Active' : 'Inactive';
                })
            
            ->rawColumns(['region', 'marketplace_id', 'name'])
            ->make(true);
        }

        return view('admin.credential.index');
    }
}
