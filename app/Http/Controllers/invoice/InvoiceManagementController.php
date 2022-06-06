<?php

namespace App\Http\Controllers\invoice;

use RedBeanPHP\R;
use League\Csv\Reader;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class InvoiceManagementController extends Controller
{
    public function Index(request $request)
    {   
        
        if ($request->ajax()) {
            $data = Invoice::get();
            foreach($data as $key => $value){
                $result[$key]['id'] = $value;
            }
            return DataTables::of($data)
                ->addIndexColumn()
                
                ->addColumn('action', function ($id) use ($result) {

                    $action = '<div class="pl-2"><input class="" type="checkbox" value='.$id['id'].' name="options[]" ></div>';
                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('invoice.index');
    }

    public function Upload()
    {
        return view('invoice.upload_excel');
    }

    public function showpdf(Request $request )
    {
        return view('invoice.template');
    }

    public function showTemplate(Request $request)
    {
        $data = explode('-', $request->id);
        foreach($data as $id)
        {
            $data = Invoice::where('id', $id)->get();
        }

        return view('invoice.template', compact(['data']));
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
        // $data = file_get_contents($file);

        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');
     
     R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
       
     $data = Excel::toArray([], $file);

     $header = [];
     $result = [];
     $check = ['.', '(', ')'];
     foreach($data[0][0] as $key => $value)
     {  
         if($value) {
            $testing = str_replace(' ', '_', trim($value));
             $header[$key] = str_replace($check,'',strtolower($testing));
         }
         
     } 
     // po($header);
     foreach($data as $result)
     {    
          foreach($result as $key2 => $record)
          {
           
               if($key2 != 0 )
               {
                    $invoice = R::dispense('invoices');
                    foreach($record as $key3 => $value)
                    {
                         $name = (isset($header[$key3])) ? $header[$key3] : null;
                         if($name)
                         {
                               $invoice->$name = $value;  
                         }
                    }   
                    R::store($invoice);
               }
          }
     }
        
     return response()->json(["success" => "all file uploaded successfully"]);
    }

    public function ExportPdf(Request $request)
    {
        $inc = str_replace('https://amazon-sp-api-laravel.app/invoice/convert-pdf/','', $request->id);

        $path = 'invoice'.$inc;
        $exportToPdf = $path. '.pdf';
        Browsershot::url($request->id)
       ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
       ->noSandbox()
       ->showBackground()
       ->save($exportToPdf);
       

       return response()->json(["success" => "Export to PDF Successfully"]);
       
    }
}
