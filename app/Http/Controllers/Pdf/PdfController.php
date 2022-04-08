<?php

namespace App\Http\Controllers\Pdf;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class PdfController extends Controller
{
    public function uploadview()
    {
        return view('pdfview.index');
    }
    public function viewupload()
    {
        return view('pdfview.view');
    }
 

    public function testupl(Request $request)
    {
        // if ($request->hasfile('filenames')) {
        //     foreach ($request->file('filenames') as $file) 
        //     {
        //         $name = time().'.'.$file->extension();
        //         $file->move(public_path().'/files/', $name);  
        //         $data[] = $name;  
        //     }
        // }
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:10000'
        ]);
        $msg = "Pdf Upload has been completed!";
        $source = file_get_contents($request->pdf);
        $path = 'PdfMaster/Test.pdf';
        Storage::put($path, $source);
        return redirect('/pdfview/index')->with('success', 'All Pdf uploaded successfully');
    }
}
