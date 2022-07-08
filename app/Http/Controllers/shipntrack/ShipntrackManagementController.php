<?php

namespace App\Http\Controllers\shipntrack;

use File;
use Excel;
use RedBeanPHP\R;
use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\controller;
use Illuminate\Support\Facades\Storage;

class ShipntrackManagementController extends Controller
{
    public function index()
    {
        return view('shipntrack.manage');
    }

    public function uploadCsv(Request $request)
    {
        $files =  $request->files;
        $arr = [];
        
        foreach($files as $key => $file)
        {
            foreach($file as $keys => $value)
            {
            }
        }

        $path = 'ShipnTrack/export-rate.csv';
        $data= file_get_contents($value);
        Storage::put($path, $data);
        $csv = Reader::createFromPath($value, 'r');
        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);
        
        $host = config('database.connections.ship.host');
        $dbname = config('database.connections.ship.database');
        $port = config('database.connections.ship.port');
        $username = config('database.connections.ship.username');
        $password = config('database.connections.ship.password');
        
        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
    
        foreach($csv as $data)
        {   
            $shipntrack = R::dispense('rakemasters');
            foreach($data as $key => $result)
            {
                $header = str_replace(' ', '_', strtolower($key));
                if($header)
                {
                    $shipntrack->$header = $result;
                    R::store($shipntrack);
                }     
            }
        }
    }
}
