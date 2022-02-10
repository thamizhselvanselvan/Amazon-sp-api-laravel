<?php

namespace App\Http\Controllers\Admin;

use App\Models\Mws_region;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class RegionController extends Controller
{
    //
    public function index(Request $request)
    {   
        if($request-> ajax()){

        
        $getData = ['id', 'region', 'region_code', 'url', 'site_url', 'marketplace_id', 'currency_id', 'status'];

        $data = Mws_region::with(['currency'])->latest()->get($getData);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                return ($row->status) ? 'Active' : 'Inactive';
            })
            ->editColumn('currency', function ($row) {

                return $row->currency->name;
            })
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
           
            ->rawColumns(['url', 'status', 'currency'])
            ->make(true);

        }
        return view('admin.region.index');
    }
}
