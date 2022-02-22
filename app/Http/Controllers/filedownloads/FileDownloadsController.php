<?php

namespace App\Http\Controllers\filedownloads;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileDownloadsController extends Controller
{
    public function filedownloads()
    {
        return view('filedownloads.index');
    }
}
