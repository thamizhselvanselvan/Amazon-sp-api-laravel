<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CatalogDashboardController extends Controller
{
    public function Metrics()
    {
        $cat_dashboard_file = "excel/downloads/catalog-dashboard-file.json";
        $json_arrays = json_decode(Storage::get($cat_dashboard_file));
        
       return view('Catalog.Dashboard.index', compact('json_arrays'));
    }
}
