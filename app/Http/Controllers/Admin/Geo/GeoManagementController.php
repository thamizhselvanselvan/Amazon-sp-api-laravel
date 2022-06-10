<?php

namespace App\Http\Controllers\Admin\Geo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class GeoManagementController extends Controller
{
    public function index()
    {
        return view('admin.geoManagement.geo');
    }
}
