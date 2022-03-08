<?php

namespace App\Http\Controllers\otherProduct;

use Illuminate\Http\Request;
use App\Models\OthercatDetails;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class anotherAmazonProduct extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            $data = OthercatDetails::query();
            return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('availability', function ($row) {
                return $row ? 'Available' : 'NA';
            })
            ->make(true);

        }
        return view('amazonOtherProduct.index');
    }

    public function exportOtherProduct()
    {
        // $data = OthercatDetails::limit(100)->get();
        
    }
}
