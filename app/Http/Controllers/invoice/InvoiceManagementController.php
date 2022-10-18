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
use Picqer\Barcode\BarcodeGeneratorHTML;
use Type;
use Yajra\DataTables\Facades\DataTables;


class InvoiceManagementController extends Controller
{
    public function Index(request $request)
    {

        // if ($request->ajax()) {
        //     // $data = Invoice::orderBy('id', 'DESC')->get();
        //     $data = DB::connection('web')->select("select DISTINCT * from invoices order by id DESC");
        //     foreach ($data as $key => $value) {
        //         $result[$key]['id'] = $value;
        //     }
        //     return DataTables::of($data)
        //         ->addIndexColumn()
        //         ->addColumn('action', function ($id) use ($result) {

        //             $action = '<div class="d-flex"><a href="/invoice/convert-pdf/' . $id->invoice_no . ' " class="edit btn btn-success btn-sm" target="_blank"><i class="fas fa-eye"></i> View </a>';
        //             $action .= '<div class="d-flex pl-2"><a href="/invoice/download-direct/' . $id->invoice_no . ' " class="edit btn btn-info btn-sm"><i class="fas fa-download"></i> Download </a>';
        //             $action .= '<div class="d-flex pl-2"><a href="/invoice/edit/' . $id->invoice_no . ' " class=" btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit </a>';
        //             return $action;
        //         })
        //         ->rawColumns(['action'])
        //         ->make(true);
        // }
        // return view('invoice.index');
        $mode = DB::connection('web')->select("SELECT mode from invoices group by mode");
        return view('invoice.search_invoice', compact('mode'));
        // $this->SearchInvoice();
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
        if ($request->ajax()) {
            $mode = $request->invoice_mode;
            $bag_no = $request->bag_no;
            $Invoice_date = $request->invoice_date;
            $results = '';
            // $results = DB::connection('web')->select("SELECT id, invoice_no, invoice_date, mode, channel, shipped_by, awb_no, store_name, bill_to_name, ship_to_name, sku, qty, currency, product_price FROM invoices WHERE mode = '$mode' and invoice_date BETWEEN '$date1' AND '$date2' "); 
            $results = Invoice::when(!empty(trim($request->invoice_mode)), function ($query) use ($mode) {
                $query->where('mode', $mode);
            })
                ->when(!empty(trim($request->bag_no)), function ($query) use ($bag_no) {
                    $query->where('bag_no', $bag_no);
                })
                ->when(!empty(trim($request->invoice_date)), function ($query) use ($Invoice_date) {
                    $date = $this->split_date($Invoice_date);
                    $query->whereBetween('invoice_date', [$date[0], $date[1]]);
                })
                ->get();
        }
        // Log::alert(json_encode($results));
        return response()->json($results);
    }

    public function split_date($date)
    {
        $newdate = explode(' - ', $date);
        return ([trim($newdate[0]), trim($newdate[1])]);
    }

    public function showpdf(Request $request)
    {
        return view('invoice.invoice');
    }

    public function showTemplate(Request $request)
    {
        $id = $request->id;

        $data = $this->invoiceDataFormating("'$id'", type: 'Single');
        $data = $data[0];
        $value = $data;
        $invoice_no = $data['invoice_no'];
        $awb_no = $data['awb_no'];
        $mode = $data['mode'];
        $invoice_mode = strtolower($mode);

        $generator = new BarcodeGeneratorHTML();
        $invoice_bar_code = $generator->getBarcode($invoice_no, $generator::TYPE_CODE_128);
        $bar_code = $generator->getBarcode($awb_no, $generator::TYPE_CODE_128);

        if ($invoice_mode != '') {
            return view('invoice.' . $invoice_mode, compact(['value'], 'invoice_no', 'invoice_bar_code', 'bar_code'));
        }
    }

    public function selectedPrint($id)
    {
        $eachid = explode('-', $id);
        $each_id_array = "'" . implode("','", $eachid) . "'";

        $generator = new BarcodeGeneratorHTML();
        $result = $this->invoiceDataFormating($each_id_array);

        foreach ($result as $details) {

            $invoice_mode = $details['mode'];
            $invoice_mode_multi = strtolower($invoice_mode);

            $invoice_bar_code[] = $generator->getBarcode($details['invoice_no'], $generator::TYPE_CODE_128);
            $awb_bar_code[] = $generator->getBarcode($details['awb_no'], $generator::TYPE_CODE_128);

            $record[] = $details;
        }

        if ($invoice_mode_multi != '') {
            return view('invoice.multiple' . $invoice_mode_multi, compact(['record'], 'invoice_bar_code', 'awb_bar_code'));
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
        $this->deleteAllPdf();

        $passid = $request->id;
        $currenturl = request()->getSchemeAndHttpHost();

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:invoice-bulk-zip-download $passid $currenturl > /dev/null &";
            exec($command);
        } else {
            Artisan::call('pms:invoice-bulk-zip-download' . ' ' . $passid . ' ' . $currenturl);
        }

        return response()->json(['success' => 'zip created successfully']);
    }

    public function zipDownload()
    {
        if (!Storage::exists('invoice/zip/invoice.zip')) {
            return redirect()->intended('/invoice/search-invoice')->with('success', 'File is not available right now! Please wait.');
        }
        return Storage::download('invoice/zip/invoice.zip');
    }


    public function DirectDownloadPdf(Request $request, $id)
    {
        $this->deleteAllPdf();
        // $data = Invoice::where('id', $id)->get();
        $data = DB::connection('web')->select("SELECT * from invoices where invoice_no = '$id' ");
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
        $this->deleteAllPdf();

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
        $filepath = public_path('template/Invoice-Template.xlsx');
        return Response()->download($filepath);
    }

    public function invoiceDataFormating($id, $type = 'bulk')
    {
        $invoice_details = [];
        $grand_total = 0;

        $invoice_no = $id;

        if ($type == 'bulk') {
            $data = DB::connection('web')->select("SELECT invoice_no from invoices where id IN ($id) ");
            $invoice_no = [];
            foreach ($data as $key => $value) {
                $invoice_no[] = "$value->invoice_no";
            }
            $invoice_no =  "'" . implode("','", $invoice_no) . "'";
        }

        $invoice_data_array = DB::connection('web')
            ->select(
                "SELECT 
             invoice_no,
            GROUP_CONCAT(DISTINCT invoice_date) as invoice_date,
            GROUP_CONCAT(DISTINCT mode) as mode,
            GROUP_CONCAT(DISTINCT channel) as channel,
            GROUP_CONCAT(DISTINCT shipped_by) as shipped_by,
            GROUP_CONCAT(DISTINCT awb_no) as awb_no,
            GROUP_CONCAT(DISTINCT arn_no) as arn_no,
            GROUP_CONCAT(DISTINCT store_name) as store_name,
            GROUP_CONCAT(DISTINCT store_add) as store_add,
            GROUP_CONCAT(DISTINCT bill_to_name) as bill_to_name,
            GROUP_CONCAT(DISTINCT bill_to_add) as bill_to_add,
            GROUP_CONCAT(DISTINCT ship_to_name) as ship_to_name,
            GROUP_CONCAT(DISTINCT ship_to_add) as ship_to_add,
            GROUP_CONCAT(sku SEPARATOR '-invoice-') as sku,
            GROUP_CONCAT(item_description SEPARATOR '-invoice-') as item_description,
            GROUP_CONCAT(hsn_code SEPARATOR '-invoice-') as hsn_code,
            GROUP_CONCAT(qty SEPARATOR '-invoice-') as qty,
            GROUP_CONCAT(currency SEPARATOR '-invoice-') as currency,
            GROUP_CONCAT(product_price SEPARATOR '-invoice-') as product_price,
            GROUP_CONCAT(taxable_value SEPARATOR '-invoice-') as taxable_value,
            GROUP_CONCAT(total_including_taxes SEPARATOR '-invoice-') as total_including_taxes,
            GROUP_CONCAT(grand_total SEPARATOR '-invoice-') as grand_total,
            GROUP_CONCAT(no_of_pcs SEPARATOR '-invoice-') as no_of_pcs,
            GROUP_CONCAT(packing SEPARATOR '-invoice-') as packing,
            GROUP_CONCAT(dimension SEPARATOR '-invoice-') as dimension,
            GROUP_CONCAT(actual_weight SEPARATOR '-invoice-') as actual_weight,
            GROUP_CONCAT(charged_weight SEPARATOR '-invoice-') as charged_weight,
            GROUP_CONCAT(clientcode SEPARATOR '-invoice-') as clientcode
             from invoices where invoice_no IN ($invoice_no)
             group by invoice_no"
            );


        $item_details[] = [
            'item_description' => NULL,
            'hsn_code' => NULL,
            'qty' => NULL,
            'currency' => NULL,
            'product_price' => NULL,
            'taxable_value' => NULL,
            'total_including_taxes' => NULL,
            'grand_total' => NULL,
            'no_of_pcs' => NULL,
            'packing' => NULL,
            'dimension' => NULL,
            'actual_weight' => NULL,
            'charged_weight' => NULL,
        ];

        $item_details_final_array = [];
        $grand_total = 0;
        foreach ($invoice_data_array as $key => $value) {
            $grand_total = 0;
            foreach ($value as $key1 => $details) {

                if (array_key_exists($key1, $item_details[0])) {

                    $product_array = explode('-invoice-', $details);
                    if ($key1 == 'total_including_taxes') {

                        foreach ($product_array as $key2 => $val) {
                            $grand_total += (int) $val;
                        }
                    } else {
                        foreach ($product_array as $key2 => $val) {
                            $item_details[$key2][$key1] = $val;
                        }
                    }
                } else {
                    $invoice_details[$key1] =  str_ireplace('-invoice-', '', $details);
                }
            }
            $invoice_details['grand_total'] = $grand_total;
            $invoice_details['product_details'] = $item_details;
            $item_details_final_array[$key] = $invoice_details;
            $invoice_details = [];
        }
        // dd($item_details_final_array);
        return $item_details_final_array;
    }

    public function edit($id)
    {
        // $url = URL::previous();
        $data = DB::connection('web')->select("SELECT * FROM invoices WHERE invoice_no = '$id' ");
        return view('invoice.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $invoice = invoice::find($id);
        $url = $request->url;

        $invoice->invoice_no = $request->invoice_no;
        $invoice->invoice_date = $request->invoice_date;
        $invoice->mode = $request->mode;
        $invoice->channel = $request->channel;
        $invoice->shipped_by = $request->shipped_by;
        $invoice->awb_no = $request->awb_no;
        $invoice->arn_no = $request->arn_no;
        $invoice->store_name = $request->store_name;
        $invoice->store_add = $request->store_add;
        $invoice->bill_to_name = $request->bill_to_name;
        $invoice->bill_to_add = $request->bill_to_add;
        $invoice->ship_to_name = $request->ship_to_name;
        $invoice->ship_to_add = $request->ship_to_add;
        $invoice->sku = $request->sku;
        $invoice->item_description = $request->item_description;
        $invoice->hsn_code = $request->hsn_code;
        $invoice->qty = $request->qty;
        $invoice->currency = $request->currency;
        $invoice->product_price = $request->product_price;
        $invoice->taxable_value = $request->taxable_value;
        $invoice->total_including_taxes = $request->total_including_taxes;
        $invoice->grand_total = $request->grand_total;
        $invoice->no_of_pcs = $request->no_of_pcs;
        $invoice->packing = $request->packing;
        $invoice->dimension = $request->dimension;
        $invoice->actual_weight = $request->actual_weight;
        $invoice->charged_weight = $request->charged_weight;
        $invoice->update();


        // if($request->url =='https://amazon-sp-api-laravel.app/invoice/manage'){

        return redirect()->intended('/invoice/manage')->with('success', 'Invoice  has been updated successfully');
        // }
        // else{
        //     return redirect()->intended('/invoice/search-invoice')->with('success', 'Invoice  has been updated successfully');
        // }
    }

    public function deleteAllPdf()
    {
        $files = glob(Storage::path('invoice/*'));
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
