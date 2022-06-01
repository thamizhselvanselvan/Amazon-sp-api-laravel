<?php

namespace App\Http\Controllers\invoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoiceManagementController extends Controller
{
    public function Index()
    {
        return view('invoice.index');
    }

    public function Upload()
    {
        return view('invoice.upload_excel');
    }

    public function UploadExcel(Request $request)
    {
        foreach ($request->files as $key => $files) {

            foreach ($files as $keys => $file) {
                // $file_extension = $file->getClientOriginalExtension();
                // if ($file_extension == '') 
                {

                    $fileName = $file->getClientOriginalName();
                    $fileName = uniqid() . ($fileName);
                    // $desinationPath = 'BOE/' . $company_id . '/' . $year . '/' . $month . '/' . $fileName;
                    // Storage::put($desinationPath,  file_get_contents($file));
                }
            }
        }
        return file_get_contents($fileName);
    }
}
