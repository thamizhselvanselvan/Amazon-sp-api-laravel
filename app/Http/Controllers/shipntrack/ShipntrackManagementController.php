<?php

namespace App\Http\Controllers\shipntrack;

use File;
use Excel;
use RedBeanPHP\R;
use League\Csv\Reader;
use League\Csv\Statement;

use App\Models\Ratemaster;
use Illuminate\Http\Request;

use Illuminate\Http\Response;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ShipntrackManagementController extends Controller
{
    public function Index(Request $request)
    {
         $sourcedestination = DB::connection('shipntracking')->select("SELECT source_destination FROM ratemasters group by source_destination ");
        //  po($sourcedestination);
         return view('shipntrack.index', compact('sourcedestination'));
    }
    
    public function GetDataTable(Request $request)
    {
        $option = $request->option;
        if($request->ajax())
        {
            $shipntrack_data = '';
            $shipntrack_data = DB::connection('shipntracking')->select("SELECT * FROM ratemasters WHERE source_destination = '$option' ");
        
        }
        return response()->json($shipntrack_data);
    }
    
    public function upload()
    {
        return view('shipntrack.manage');
    }
    
    public function uploadCsv(Request $request)
    {
        $files =  $request->files;
        
        foreach($files as $key => $file)
        {
            foreach($file as $keys => $value)
            {
            }
        }

        $path = 'ShipnTrack/export-rate.csv';
        $data= file_get_contents($value);
        if(!Storage::exists($path))
        {
            Storage::put($path, '');
        }
        Storage::put($path, $data);
        
        if(App::environment(['Production', 'Staging', 'production', 'staging']))
        {
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:shipntrack-csv-upload > /dev/null &";
            exec($command);
        }else{
            Artisan::call('pms:shipntrack-csv-upload');
        }
        
        return response()->json(['success' => 'File upload successfully']);
    }

    public function templateDownload()
    {
        return Response()->download(public_path('shipntrackCSV/Export-Rate.csv'));
    }

}
