<?php

namespace App\Http\Controllers\filedownloads;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user()->email;
        $path = "app/excel/downloads/otheramazon/".$user;
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

    

   
    public function download_other_product($id)
    {
        //Other Amazon file download
        // $file_path = "excel/downloads/otheramazon/otherProductDetails".$id.'.csv';
        $user = Auth::user()->email;
        $file_path = "excel/downloads/otheramazon/".$user.'/' . $id;
        //$path = Storage::path($file_path);
        if (Storage::exists($file_path)) {
            return Storage::download($file_path);
        }
        return 'file not exist';
    }
}
