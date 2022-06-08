<?php

namespace App\Http\Controllers\buybox;

use App\Http\Controllers\Controller;
use App\Models\Aws_credential;
use App\Models\Inventory\Country;
use App\Models\Mws_region;
use Illuminate\Http\Request;

class BuyboxAsinMasterController extends Controller
{
    //
    public function index()
    {
        $country = Mws_region::get();
        // dd($country);
        return view('buybox.asinmaster', compact('country'));
    }
}
