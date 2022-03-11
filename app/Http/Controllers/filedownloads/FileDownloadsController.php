<?php

namespace App\Http\Controllers\filedownloads;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileDownloadsController extends Controller
{
    public function filedownloads()
    {
        // date_default_timezone_set('Asia/Kolkata');
        $path = "app/excel/downloads/otheramazon/";
        $path = (storage_path($path));
        $files = (scandir($path));

        $filesArray = [];
        foreach ($files as $key => $file) {
            if ($key > 1) {

                $filesArray[][$file] =  date("F d Y H:i:s.", filemtime($path . '/' . $file));
            }
        }


        return view('filedownloads.index', compact('filesArray'));
    }

    public function other_file_download()
    {
        $path = "app/excel/downloads/otheramazon/";
        $path = storage_path($path);
        $files = (scandir($path));

        $filesArray = [];
        foreach ($files as $key => $file) {
            if ($key > 1) {

                $filesArray[][$file] =  date("F d Y H:i:s.", filemtime($path . '/' . $file));
            }
        }

        return response()->json(['success' => true, "files_lists" => $filesArray]);
    }

    public function download_universalTextiles()
    {

        $file_path = "excel/downloads/universalTextilesExport.csv";
        //$path = Storage::path($file_path);
        if (Storage::exists($file_path)) {
            return Storage::download($file_path);
        }
        return 'file not exist';
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

    public function download_other_product($id)
    {
        //Other Amazon file download
        // $file_path = "excel/downloads/otheramazon/otherProductDetails".$id.'.csv';
        $file_path = "excel/downloads/otheramazon/" . $id;
        //$path = Storage::path($file_path);
        if (Storage::exists($file_path)) {
            return Storage::download($file_path);
        }
        return 'file not exist';
    }
}
