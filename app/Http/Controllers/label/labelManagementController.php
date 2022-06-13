<?php

namespace App\Http\Controllers\label;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class labelManagementController extends Controller
{
    public function manage()
    {
        return view('label.manage');
    }
}
