<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\aws_credentials;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CredentialsController extends Controller
{
    public function index(Request $request)
    { 
        if($request->ajax()){
            
            $getData = ['id', 'store_name', 'merchant_id','verified','status'];
            $data = aws_credentials::with(['currency', 'mws_region'])->orderby('status', 'DESC')->get($getData);
        
            return DataTables::of($data)
                ->addIndexcolumn()
                // ->editColumn('region', function ($row){
                //     return ($row->mws_region->region) ;
                // })
                // ->addColumn('marketplace_id', function ($row){
                //     return ($row->marketplace_id) ;
                // })
                // ->addColumn('name', function ($row){
                //     return ($row->name) ;
                // })
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
