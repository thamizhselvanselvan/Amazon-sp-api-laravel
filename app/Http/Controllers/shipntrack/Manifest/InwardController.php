<?php

namespace App\Http\Controllers\shipntrack\Manifest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShipNtrack\Process\Process_Master;

class InwardController extends Controller
{
    public function index(Request $request)
    {
        return view('shipntrack.manifest.inward.index');
    }
    public function inw_view(Request $request)
    {
        $destinations  = Process_Master::query()->get();
        return view('shipntrack.manifest.inward.add',compact('destinations'));
    }
}
