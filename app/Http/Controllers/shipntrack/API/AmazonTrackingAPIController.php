<?php

namespace App\Http\Controllers\shipntrack\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AmazonTrackingAPIController extends Controller
{
    public function index(Request $request)
    {
        // return ['First Name :' => $request->first_name, 'Last Name :' => $request->last_name];
        return "<h1>".$request->first_name."</h1><h2>".$request->last_name."</h2>";
    }
}
