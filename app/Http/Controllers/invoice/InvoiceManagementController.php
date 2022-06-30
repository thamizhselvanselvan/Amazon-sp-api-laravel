<?php

namespace App\Http\Controllers\invoice;

use App\Models\Invoice;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

use Spatie\Browsershot\Browsershot;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Yajra\DataTables\Facades\DataTables;

use File;
use DateTime;
use ZipArchive;
use RedBeanPHP\R;
use League\Csv\Reader;


class InvoiceManagementController extends Controller
{
    public function Index(request $request)
    {

        if ($request->ajax()) {
            // $data = Invoice::orderBy('id', 'DESC')->get();
            $data = DB::connection('web')->select("select * from invoices order by id DESC");
            foreach ($data as $key => $value) {
                $result[$key]['id'] = $value;
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($id) use ($result) {

                    // $action1 = '<div class="pl-2"><input class="" type="checkbox" value='.$id['id'].' name="options[]" ></div>';
                    $action = '<div class="d-flex"><a href="/invoice/convert-pdf/' . $id->id . ' " class="edit btn btn-success btn-sm" target="_blank"><i class="fas fa-eye"></i> View </a>';
                    $action .= '<div class="d-flex pl-2"><a href="/invoice/download-direct/' . $id->id . ' " class="edit btn btn-info btn-sm"><i class="fas fa-download"></i> Download </a>';
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

    public function SearchInvoice()
    {
        $mode = DB::connection('web')->select("SELECT mode from invoices group by mode");
        return view('invoice.search_invoice', compact('mode'));
    }

    public function SearchDateWiseInvoice(Request $request)
    {
        if($request->ajax())
        {
            $mode = $request->invoice_mode;

            $date = $request->invoice_date;
            $newdate = explode(' - ', $date);
            $date1 = $newdate[0];
            $date2 = $newdate[1];

            $results = DB::connection('web')->select("SELECT id, invoice_no, invoice_date, mode, channel, shipped_by, awb_no, store_name, bill_to_name, ship_to_name, sku, qty, currency, product_price FROM invoices WHERE mode = '$mode' and invoice_date BETWEEN '$date1' AND '$date2' ");
        }
        return response()->json($results);
    }

    public function showpdf(Request $request)
    {
        return view('invoice.invoice');
    }

    public function showTemplate(Request $request)
    {
        $id = $request->id;
        // $data = Invoice::where('id', $id)->get();
        $data = DB::connection('web')->select("SELECT * from invoices where id ='$id' ");
        $invoice_no = $data[0]->invoice_no;
        $awb_no = $data[0]->awb_no;
        $mode = $data[0]->mode;
        $invoice_mode = strtolower($mode);

        $generator = new BarcodeGeneratorHTML();
        $invoice_bar_code = $generator->getBarcode($invoice_no, $generator::TYPE_CODE_128);
        $bar_code = $generator->getBarcode($awb_no, $generator::TYPE_CODE_128);

        if($invoice_mode!= ''){
            return view('invoice.'.$invoice_mode , compact(['data'],'invoice_no', 'invoice_bar_code', 'bar_code'));
        }
    }

    public function selectedPrint($id)
    {
        $eachid = explode('-', $id);
        foreach ($eachid as $id) {
            // $data []= Invoice::where('id', $id)->get();
            $data []= DB::connection('web')->select("SELECT * from invoices where id ='$id' ");
            $invoice_mode = $data[0][0]->mode;
            $invoice_mode_multi = strtolower($invoice_mode);
        }
        foreach($data as $key => $record)
        {
            $invoice_no = $record[0]->invoice_no;
            $awb_no = $record[0]->awb_no;
            $generator = new BarcodeGeneratorHTML();
            $invoice_bar_code []= $generator->getBarcode($invoice_no, $generator::TYPE_CODE_128);
            $awb_bar_code []= $generator->getBarcode($awb_no, $generator::TYPE_CODE_128);
        }
        if($invoice_mode_multi!= ''){
            return view('invoice.multiple'.$invoice_mode_multi , compact(['data'], 'invoice_bar_code', 'awb_bar_code'));
        }
    }

    public function UploadExcel(Request $request)
    {
        foreach ($request->files as $key => $files) {

            foreach ($files as $keys => $file) {

                $fileName = $file->getClientOriginalName();
                $fileName = uniqid() . ($fileName);
            }
        }
        $data = file_get_contents($file);
        $path = 'invoiceExcel/invoice.xlsx';
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }
        Storage::put($path, $data);

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:invoice-excel-import > /dev/null &";
            exec($command);
        } else {

            Artisan::call('pms:invoice-excel-import');
        }

        return response()->json(["success" => "All file uploaded successfully"]);
    }

    // Begin download all selected rows 
    public function SelectedDownload(Request $request)
    {
        // echo 'working file';
        $passid = $request->id;
        $currenturl =  URL::current();

        $excelid = explode('-', $passid);

        foreach ($excelid as $getId) {
            // $id = Invoice::where('id', $getId)->get();
            $id = DB::connection('web')->select("SELECT * from invoices where id ='$getId' ");
            foreach ($id as $key => $value) {
                $invoice_no = $value->invoice_no;
                $url = str_replace('select-download', 'convert-pdf', $currenturl . '/' . $getId);
                $path = 'invoice/invoice' . $invoice_no . '.pdf';
                if (!Storage::exists($path)) {
                    Storage::put($path, '');
                }
                $exportToPdf = storage::path($path);
                Browsershot::url($url)
                    // ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
                    ->showBackground()
                    ->savePdf($exportToPdf);

                $saveAsPdf[] = 'invoice' . $invoice_no . '.pdf';
            }
        }

        return response()->json($saveAsPdf);
    }

    public function zipDownload($arr)
    {
        $replace = explode(',', $arr);
        $zip = new ZipArchive;
        $path = 'zip/' . 'invoice.zip';
        $fileName = Storage::path('zip/' . 'invoice.zip');
        Storage::delete($path);
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }
        if ($zip->open($fileName, ZipArchive::CREATE) === TRUE) {
            foreach ($replace as $key => $value) {
                $path = Storage::path('invoice/' . $value);
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
        $path = storage::path('invoice/invoice' . $invoice_no);
        $exportToPdf = $path . '.pdf';
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
        $file_path =  'invoice/invoice' . $id . '.pdf';
        if (!Storage::exists($file_path)) {
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
        return Storage::download('invoice/invoice' . $invoice_no . '.pdf');
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
        $fileName = Storage::path('zip/' . 'invoice.zip');
        return response()->download($fileName);
    }

    public function downloadTemplate()
    {
        return Storage::download('Invoice_Excel_Template.xlsx');
    }
}
