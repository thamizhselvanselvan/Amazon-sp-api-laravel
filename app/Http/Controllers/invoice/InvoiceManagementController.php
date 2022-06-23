<?php

namespace App\Http\Controllers\invoice;

use File;
use DateTime;
use ZipArchive;
use RedBeanPHP\R;
use League\Csv\Reader;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class InvoiceManagementController extends Controller
{
    public function Index(request $request)
    {   
        
        if ($request->ajax()) {
            // $data = Invoice::orderBy('id', 'DESC')->get();
            $data = DB::connection('web')->select("select * from invoices order by id DESC");
            foreach($data as $key => $value){
                $result[$key]['id'] = $value;
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($id) use ($result) {

                    // $action1 = '<div class="pl-2"><input class="" type="checkbox" value='.$id['id'].' name="options[]" ></div>';
                    $action = '<div class="d-flex"><a href="/invoice/convert-pdf/' . $id->id .' " class="edit btn btn-success btn-sm" target="_blank"><i class="fas fa-eye"></i> View </a>';
                    $action .= '<div class="d-flex pl-2"><a href="/invoice/download-direct/' . $id->id .' " class="edit btn btn-info btn-sm"><i class="fas fa-download"></i> Download </a>';
                    return $action;
                })
                // ->addColumn('check_box', function ($id) use ($result) {

                //     $check_box = '<div class="pl-2"><input class="check_options" type="checkbox" value='.$id['id'].' name="options[]" ></div>';
                //     return $check_box;
                // })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('invoice.index');
    }

    public function Upload()
    {
        return view('invoice.upload_excel');
    }

    public function SearchInvoice()
    {
        return view('invoice.search_invoice');
    }

    public function GetInvoice(Request $request)
    {
        if($request->ajax())
        {
            $date = $request->invoice_date;
            $newdate = explode( ' - ' ,$date);
            $date1 = $newdate[0];
            $date2 = $newdate[1];
            $results = DB::connection('web')->select("SELECT id, invoice_no, invoice_date, channel, shipped_by, awb_no, arn_no, hsn_code, qty, product_price FROM invoices WHERE invoice_date BETWEEN '$date1' AND '$date2' ");
            
        }
        return response()->json($results);
    }
    public function showpdf(Request $request )
    {
        return view('invoice.invoice');
    }

    public function showTemplate(Request $request)
    {
        $id = $request->id;
        // $data = Invoice::where('id', $id)->get();
        $data = DB::connection('web')->select("SELECT * from invoices where id ='$id' ");
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
        //  po($header);
        foreach($data as $result)
        {    
            foreach($result as $key2 => $record)
            {
                $invoice_number = $record[0];
                if($key2 != 0 )
                { 
                    $id = NULL;
                    // $Totaldata = Invoice::where('invoice_no', $record[0])->get();
                    $Totaldata = DB::connection('web')->select("SELECT * from invoices where invoice_no ='$invoice_number' ");
                    
                    if(isset($Totaldata[0]))
                    {
                        $id = $Totaldata[0]->id;
                    }
                    $invoice = R::dispense('invoices');
                
                    if($id == NULL)
                    { 
                        foreach($record as $key3 => $value)
                        {   
                            $name = (isset($header[$key3])) ? $header[$key3] : null;
                            if($name)
                            {
                                $invoice->$name = $value;  
                                
                                if(isset($header[1]))
                                {
                                    $dateset = $header[1];
                                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($record[1])->format("Y-m-d");
                                    $invoice->$dateset = $date;
                                }
                            } 
                        } 
                        R::store($invoice);  
                    } 
                    else
                    {
                        $update = R::load('invoices', $id);
                        foreach($record as $key3 => $value)
                        {
                            $name = (isset($header[$key3])) ? $header[$key3] : null;
                            if($name)
                            {
                                $update->$name = $value ; 
                                if(isset($header[1]))
                                {
                                    $dateset = $header[1];
                                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($record[1])->format("d/m/Y");
                                    $update->$dateset = $date;
                                }
                            }
                        }
                        R::store($update);  
                    } 
                }
            }
        }  
        return response()->json(["success" => "all file uploaded successfully"]);
    }
    // Begin download all selected rows 
        public function SelectedDownload(Request $request)
        {
            // echo 'working file';
            $passid = $request->id;
            $currenturl =  URL::current();
            
            $excelid = explode('-', $passid);
            
            foreach($excelid as $getId)
            {
                // $id = Invoice::where('id', $getId)->get();
                $id = DB::connection('web')->select("SELECT * from invoices where id ='$getId' ");
                foreach($id as $key => $value)
                {
                    $invoice_no = $value->invoice_no;
                    $url = str_replace('select-download', 'convert-pdf', $currenturl. '/'. $getId);
                    $path = 'invoice/invoice'.$invoice_no.'.pdf';
                    if(!Storage::exists($path)) {
                        Storage::put($path, '');
                    }
                    $exportToPdf = storage::path($path);
                    Browsershot::url($url)
                    // ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
                    // ->showBackground()
                    ->savePdf($exportToPdf);
    
                    $saveAsPdf [] = 'invoice'.$invoice_no .'.pdf';
                }
            }

            return response()->json($saveAsPdf);
        }

        public function selectPrint($id)
        {
            $eachid = explode('-', $id);
            foreach($eachid as $id){
                // $data []= Invoice::where('id', $id)->get();
                $data []= DB::connection('web')->select("SELECT * from invoices where id ='$id' ");
            }
            return view('invoice.multipleInvoice', compact(['data']));
        }

        public function zipDownload( $arr)
        { 
            $replace = explode(',', $arr);
            $zip = new ZipArchive;
            $path = 'zip/'.'invoice.zip';
            $fileName = Storage::path('zip/'.'invoice.zip');
            Storage::delete($path);
            if(!Storage::exists($path))
            {
                Storage::put($path, '');
            }
            if($zip->open($fileName, ZipArchive::CREATE) === TRUE)
            {
                foreach($replace as $key => $value)
                {
                    $path = Storage::path('invoice/'.$value);
                    $relativeNameInZipFile = basename($path);
                    $zip->addFile($path, $relativeNameInZipFile);
                }
                $zip->close();
            }
            return response()->download($fileName);
        }

    // end download all selected rows

    public function DirectDownloadPdf(Request $request, $id)
    {
        // $data = Invoice::where('id', $id)->get();
        $data = DB::connection('web')->select("SELECT * from invoices where id ='$id' ");
        $invoice_no = $data[0]->invoice_no;

        $currenturl =  URL::current();
        $url = str_replace('download-direct', 'convert-pdf', $currenturl);
         $path = storage::path('invoice/invoice'.$invoice_no);
        $exportToPdf = $path. '.pdf';
        Browsershot::url($url)
        // ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
        // ->showBackground()
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
        // ->showBackground()
        ->savePdf($exportToPdf);

        return response()->json(["success" => "Export to PDF Successfully"]);
    }

    public function DownloadPdf($invoice_no)
    {
        return Storage::download('invoice/invoice'.$invoice_no.'.pdf');
    }

    public function DownloadAll()
    {
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:excel-bulkpdf-download > /dev/null &";
            exec($command);

            Log::warning("Zip Download command executed production  !!!");
        } else {
            Artisan::call('pms:excel-bulkpdf-download');
        }
        $fileName = Storage::path('zip/'.'invoice.zip');
        return response()->download($fileName);
    }

    public function downloadTemplate()
    {
        return Storage::download('Invoice_Excel_Template.xlsx');
    }

}
