<?php

namespace App\Http\Controllers\invoice;

use File;
use DateTime;
use ZipArchive;
use RedBeanPHP\R;
use League\Csv\Reader;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
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
            $data = Invoice::orderBy('id', 'DESC')->get();
            foreach($data as $key => $value){
                $result[$key]['id'] = $value;
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($id) use ($result) {

                    // $action = '<div class="pl-2"><input class="" type="checkbox" value='.$id['id'].' name="options[]" ></div>';
                    $action = '<div class="d-flex"><a href="/invoice/convert-pdf/' . $id->id .' " class="edit btn btn-success btn-sm" target="_blank"><i class="fas fa-eye"></i> View </a>';
                    $action .= '<div class="d-flex pl-2"><a href="/invoice/download-direct/' . $id->id .' " class="edit btn btn-info btn-sm"><i class="fas fa-download"></i> Download </a>';
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
        return view('invoice.invoice');
    }

    public function showTemplate(Request $request)
    {
        $id = $request->id;
        $data = Invoice::where('id', $id)->get();
        $invoice_no = $data[0]->invoice_no;
           
        return view('invoice.invoice', compact(['data'],'invoice_no'));
    }

    public function UploadExcel(Request $request)
    {
      
        foreach ($request->files as $key => $files) {

            foreach ($files as $keys => $file) {
                
                $fileName = $file->getClientOriginalName();
                $fileName = uniqid() . ($fileName);

            }
        }
        
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
                
                if(!Invoice::where('invoice_no',$record[0])->exists())
                { 
                    foreach($record as $key3 => $value)
                    { 
                        $name = (isset($header[$key3])) ? $header[$key3] : null;
                        if($name)
                        {
                            $invoice->$name = $value;  
                            // if($name == 'invoice_date')
                            // {
                            //     $date = Date(str_replace('/','-',$name));
                            //     $challan_date_formate =$date->format('Y-m-d');
                            //     $invoice->$name = $challan_date_formate;

                            // }
                        }
                        
                    } 
                    R::store($invoice);  
                } 
            }
        }
     }
        
     return response()->json(["success" => "all file uploaded successfully"]);
    }

    public function DirectDownloadPdf(Request $request, $id)
    {
        $data = Invoice::where('id', $id)->get();
        $invoice_no = $data[0]->invoice_no;

        $currenturl =  URL::current();
        $url = str_replace('download-direct', 'convert-pdf', $currenturl);
         $path = storage::path('invoice/invoice'.$invoice_no);
        $exportToPdf = $path. '.pdf';
        Browsershot::url($url)
        // ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
        ->showBackground()
        ->savePdf($exportToPdf);
        
        return $this->DownloadPdf($invoice_no);
       
        // return redirect()->back();
    }

    public function ExportPdf(Request $request)
    {
        $id = $request->invoice_no;
        $url = $request->url;
        $file_path =  'invoice/invoice'.$id.'.pdf';
        if(!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }
        // $path = storage::path('invoice/invoice'.$id);
        $exportToPdf = storage::path($file_path);
        Browsershot::url($url)
        // ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
        ->showBackground()
        ->savePdf($exportToPdf);

        return response()->json(["success" => "Export to PDF Successfully"]);

    }

    public function DownloadPdf($invoice_no)
    {
        return Storage::download('invoice/invoice'.$invoice_no.'.pdf');
    }

    public function DownloadAll()
    {
        $totalid = Invoice::all();
        foreach($totalid as $total)
        {
            $id = $total->id;
            $invoice_no = $total->invoice_no;
            $currenturl =  URL::current();
            $url = str_replace('download-all', 'convert-pdf', $currenturl. '/'.$id);
            $path = storage::path('invoice/invoice'.$invoice_no);
            $exportToPdf = $path. '.pdf';
            Browsershot::url($url)
            ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
            ->showBackground()
            ->savePdf($exportToPdf); 
            // $this->DownloadPdf($invoice_no);
        }

        // begin create zip folder for pdf

        $zip = new ZipArchive;
        $fileName = 'invoice.zip';

        if($zip->open($fileName, ZipArchive::CREATE) === TRUE)
        {
            $files = storage::files(Storage::path('invoice'));
            foreach($files as $key => $value)
            {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }
        return Storage::download($fileName);


        // end create zip folder for pdf
    }

}
