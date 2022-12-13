<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProcessManagementController extends Controller
{
    public function index()
    {
        return view('ProcessManagement.index');
    }
}
