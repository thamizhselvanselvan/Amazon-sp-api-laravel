<?php

namespace App\Http\Controllers\b2cship;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class b2cshipKycController extends Controller
{
    public function index()
    {
       
        return view('b2cship.kyc.index');
    }
}
