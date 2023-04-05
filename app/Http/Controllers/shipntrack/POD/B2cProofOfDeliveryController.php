<?php

namespace App\Http\Controllers\shipntrack\POD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class B2cProofOfDeliveryController extends Controller
{
   public function index(){
    return view('shipntrack.POD.index');
   }

   public function templete(){
    return view('shipntrack.POD.temp');
   }
}
