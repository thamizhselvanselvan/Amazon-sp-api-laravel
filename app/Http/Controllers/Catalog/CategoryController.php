<?php

namespace App\Http\Controllers\Catalog;

use League\Csv\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        return view('Cliqnshop.category');
    }

    public function export(Request $request)
    // public function export()
    {
        $request->validate([
            'category_csv' => 'required',
        ]);

        $file = file_get_contents($request->category_csv);

        $path = "Cliqnshop/category_import/category_asin.csv";

         Storage::put($path, $file);

         commandExecFunc("Mosh:Cat_Export");


        
    }
}
