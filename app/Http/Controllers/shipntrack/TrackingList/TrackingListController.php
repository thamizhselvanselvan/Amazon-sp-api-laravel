<?php

namespace App\Http\Controllers\shipntrack\TrackingList;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class TrackingListController extends Controller
{
    public function index()
    {
        return view('shipntrack.TrackingList.index');
    }
    
    public function SearchByAwbNo(Request $request)
    {
        $awbNo = $request->awbNo;
        $final_data = getTrackingDetails($awbNo);
        
        if($request->ajax())
        {
            foreach($final_data as $value)
            {
                // return DataTables::of($value);
                // po(gettype($value));
            }

        }
    }
}
