<?php

namespace App\Http\Controllers\Seller;

use App\Models\User;
use App\Models\Currency;
use App\Models\Mws_region;
use Illuminate\Http\Request;
use App\Models\Aws_credential;
use App\Models\Admin\BB\BB_User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
 
    public function index()
    {
        $mws_regions = Mws_region::with(['currency'])->get();
        $currency_lists = Currency::all();
        $awsCredentials = Aws_credential::where('seller_id', auth()->user()->id)->where('api_type', 1)->with(['mws_region'])->first();
        
        // dd($awsCredentials);
        $seller = Auth::user();
        $user = BB_User::where('id', Auth::user()->id)->with(['aws_credentials'])->first();
        $seller_email = $seller->email;
        $seller_storename = (isset($seller->aws_credentials)) ? $seller->aws_credentials->store_name : '';
        return view("seller.credentials.index", compact('awsCredentials', 'mws_regions', 'currency_lists','seller_email','seller_storename'));
        // return view('seller.credentials.index');
    }
}
