<?php

namespace App\Http\Controllers;

use App\Models\asinMaster;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class AsinMasterController extends Controller
{
    //
    public function index(Request $request){

        if($request->ajax()){

            $data = asinMaster::query();
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<a href="#' . $row->id . '" class="edit btn btn-success"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger ml-2"><i class="far fa-trash-alt"></i> Remove</button>';

                    return $actionBtn;
                })
                ->make(true);
        }
        return view('AsinMaster.index');
    }

    public function addAsin(){


        return view('AsinMaster.addAsin');
    }

    public function importBulkAsin(){

        return view('AsinMaster.importAsin');
    }

    public function addBulkAsin(Request $request){

        $request->validate([
            'asin' => 'required|mimes:csv,txt,xls,xlsx'
        ]);

        if (!$request->hasFile('asin')) {
            return back()->with('error', "Please upload file to import it to the database");
        }

        $msg = "Asin import has been completed!";
        
        $source = file_get_contents($request->asin);
        $path = 'AsinMaster/asin.csv';
        Storage::put($path, $source);

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            Log::warning("asin production executed");

            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:asin-import > /dev/null &";
            exec($command);
            Log::warning("asin production command executed");
            
        } else {

            Log::warning("Export coma executed local !");
            Artisan::call('pms:asin-import');
            
        }
       
        return redirect('/import-bulk-asin')->with('success', 'All Asins uploaded successfully');

    }

    public function exportAsinToCSV()
    {
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
            
            // exec('nohup php artisan pms:textiles-import  > /dev/null &');
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:asin-export > /dev/null &";
            exec($command);
            
            Log::warning("Export asin command executed production  !!!");
        } else {

            Log::warning("Export asin command executed local !");
            Artisan::call('pms:asin-export');
        }

        return redirect()->intended('/asin-master');


    }

    public function download_asin_master()
    {
        $file_path = "excel/downloads/asins/asinExport.csv";
        //$path = Storage::path($file_path);
        if (Storage::exists($file_path)) {
            return Storage::download($file_path);
        }
        return 'file not exist';
    }

}
