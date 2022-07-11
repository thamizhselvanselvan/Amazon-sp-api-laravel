<?php

namespace App\Http\Controllers\B2cship;

use FunctionName;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class B2cshipMonitorController extends Controller
{
    //
    public function index()
    {
        $startTime = Carbon::now()->subMinutes(60);
        $now = Carbon::now();
      
       $data = DB::connection('b2cship')->select(
        "SELECT * FROM ErrorLog 
        WHERE ErrorFrom <> ''
        AND ErrorDate BETWEEN '$startTime' AND '$now'
         ORDER BY ErrorID DESC");
       
    //    dd($data);
        return view('b2cship.monitor.index', compact('data'));
    }
}
