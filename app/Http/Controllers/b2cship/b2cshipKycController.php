<?php

namespace App\Http\Controllers\b2cship;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class b2cshipKycController extends Controller
{
    public function index()
    {
        $ans = DB::connection('mssql')->select("SELECT TOP 5 * FROM KYCStatus");
        po($ans);

        return view('b2cship.kyc.index');
    }
}
