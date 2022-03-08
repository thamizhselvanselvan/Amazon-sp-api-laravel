<?php

namespace App\Http\Controllers\filedownloads;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileDownloadsController extends Controller
{
    public function filedownloads()
    {
        return view('filedownloads.index');
    }

    public function download_universalTextiles(){

        $file_path = "excel/downloads/universalTextilesExport.csv";
        //$path = Storage::path($file_path);
        if (Storage::exists($file_path)) {
             return Storage::download($file_path);
        }
        return 'file not exist';
    }

    public function download_asin_master(){
        $file_path = "excel/downloads/asins/asinExport.csv";
        //$path = Storage::path($file_path);
        if (Storage::exists($file_path)) {
             return Storage::download($file_path);
        }
        return 'file not exist';
    }

    public function download_other_product($id){
        //Other Amazon file download
        $file_path = "excel/downloads/otheramazon/otherProductDetails".$id.'.csv';
        //$path = Storage::path($file_path);
        if (Storage::exists($file_path)) {
             return Storage::download($file_path);
        }
        return 'file not exist';
    }
}
