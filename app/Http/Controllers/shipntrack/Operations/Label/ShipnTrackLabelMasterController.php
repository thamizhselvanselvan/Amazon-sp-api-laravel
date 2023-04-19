<?php

namespace App\Http\Controllers\shipntrack\Operations\Label;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShipnTrackLabelMasterController extends Controller
{
    public function index(Request $request)
    {
        return view('shipntrack.Operation.LabelManagement.Master.index');
    }

    public function LabelMasterFormSubmit(Request $request)
    {
        po($request->file('logo')->getClientOriginalName());
    }
}
