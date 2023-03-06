<?php

namespace App\Http\Controllers\invoice;

use File;

use Type;
use DateTime;
use ZipArchive;
use RedBeanPHP\R;
use App\Models\User;
use League\Csv\Reader;

use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Models\FileManagement;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Yajra\DataTables\Facades\DataTables;


class InvoiceManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('showTemplate');
    }

    public function Index(request $request)
    {
        // $mode = DB::connection('web')->select("SELECT mode from invoices group by mode");
        $mode = Invoice::select('mode')->groupBy('mode')->get();

        if ($request->ajax()) {

            $mode = $request->invoice_mode;
            $bag_no = $request->bag_no;
            $Invoice_date = $request->invoice_date;
            $results = '';

            $currentPageNumber = $request->start / $request->length + 1;

            $results = Invoice::when(!empty(trim($request->invoice_mode)), function ($query) use ($mode) {
                $query->where('mode', $mode);
            })
                ->when(!empty(trim($request->bag_no)), function ($query) use ($bag_no) {
                    $query->where('bag_no', $bag_no);
                })
                ->when(!empty(trim($request->invoice_date)), function ($query) use ($Invoice_date) {
                    $date = $this->split_date($Invoice_date);
                    $query->whereBetween('invoice_date', [$date[0], $date[1]]);
                });

            return DataTables::of($results)
                ->addColumn('select_all', function ($result) use ($currentPageNumber) {
                    return "<input class='check_options' type='checkbox' data-current-page='$currentPageNumber' value='$result->id' name='options[]' id='checkid$result->id'>";
                })
                ->addColumn('action', function ($result) {
                    $table =
                        "<div class='d-flex'>
                            <a href='/invoice/convert-pdf/$result->invoice_no' class='edit btn btn-success btn-sm ml-2 mr-2' target='_blank'>
                                <i class='fas fa-eye'></i> View 
                            </a>
                            <a href='/invoice/download-direct/$result->invoice_no' class='edit btn btn-info btn-sm mr-2'>
                                <i class='fas fa-download'></i> Download
                            </a>
                            <a href='/invoice/edit/$result->invoice_no' class='edit btn btn-primary btn-sm mr-2'>
                                <i class='fas fa-edit'></i> Edit 
                            </a>
                        </div>";
                    return $table;
                })
                ->rawColumns(['select_all', 'action'])
                ->make(true);;
        }
        return view('invoice.search_invoice', compact('mode'));
    }

    public function Upload()
    {
        return view('invoice.upload_excel');
    }

    public function SearchInvoice()
    {
        // $mode = DB::connection('web')->select("SELECT mode from invoices group by mode");
        // return view('invoice.search_invoice', compact('mode'));
    }

    public function SearchDateWiseInvoice(Request $request)
    {
        // if ($request->ajax()) {

        //     $mode = $request->invoice_mode;
        //     $bag_no = $request->bag_no;
        //     $Invoice_date = $request->invoice_date;
        //     $results = '';

        //     // return $request->all();
        //     $currentPageNumber = $request->start / $request->length + 1;

        //     $results = Invoice::when(!empty(trim($request->invoice_mode)), function ($query) use ($mode) {
        //         $query->where('mode', $mode);
        //     })
        //         ->when(!empty(trim($request->bag_no)), function ($query) use ($bag_no) {
        //             $query->where('bag_no', $bag_no);
        //         })
        //         ->when(!empty(trim($request->invoice_date)), function ($query) use ($Invoice_date) {
        //             $date = $this->split_date($Invoice_date);
        //             $query->whereBetween('invoice_date', [$date[0], $date[1]]);
        //         })
        //         ->get();

        //     return DataTables::of($results)
        //         ->addColumn('select_all', function ($result) {
        //             return 'success';
        //         })
        //         ->addColumn('action', function ($result) {
        //             return 'success';
        //         })
        //         ->rawColumns(['select_all', 'action'])
        //         ->make(true);;
        // }
        // return response()->json($results);
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
        // $each_id_array = "'" . implode("','", $eachid) . "'";

        $generator = new BarcodeGeneratorHTML();
        $result = $this->invoiceDataFormating($eachid);

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

        $user_id = Auth::user()->id;
        $file_name = $file->getClientOriginalName();

        $file_info = [
            'user_id' => $user_id,
            'type' => 'IMPORT_INVOICE',
            'module' => 'INVOICE',
            'file_name' => $file_name,
            'file_path' => $path,
            'command_name' => 'pms:invoice-excel-import',
        ];

        FileManagement::create($file_info);
        fileManagement();

        return response()->json(["success" => "All file uploaded successfully"]);
    }

    // Begin download all selected rows
    public function SelectedDownload(Request $request)
    {
        $this->deleteAllPdf();

        $passid = $request->id;
        $mode = $request->invoice_mode;
        $invoice_date = $request->invoice_date;
        $current_page_no = $request->current_page_no;

        if ($invoice_date == '') {
            $invoice_date = 'ALL';
        }

        $invoice_date = str_replace(' ', '', $invoice_date);
        $currenturl = request()->getSchemeAndHttpHost();

        $user_id = Auth::user()->id;
        $header = ["data" => "${passid}_${currenturl}_${mode}_${invoice_date}_${current_page_no}"];
        $file_info = [
            "user_id"       => $user_id,
            "type"          => "EXPORT_INVOICE",
            "module"        => "INVOICE_EXPORT",
            "command_name"  => "pms:invoice-bulk-zip-download",
            "header"        => json_encode($header)

        ];
        FileManagement::create($file_info);
        fileManagement();

        return response()->json(['success' => 'zip created successfully']);
    }

    public function zipDownload()
    {
        $html = '';
        $html_final = '';
        $count = 0;
        $path = (Storage::path("invoice"));
        $files = scandir($path);
        foreach ($files as $key => $file) {
            if ($key > 1) {
                $file_path = Storage::path('invoice' . '/' . $file);
                if (is_dir($file_path)) {
                    $file_paths = array_slice(scandir($file_path), 2);
                    foreach ($file_paths as $sub_key => $sub_folder) {
                        $sub_folder_path = $file_path . "/$sub_folder";
                        if (is_dir($sub_folder_path)) {
                            $sub_folders = array_slice(scandir($sub_folder_path), 2);
                            foreach ($sub_folders  as $zip_path) {
                                if ($zip_path == 'zip') {
                                    $zip_path_array = scandir($sub_folder_path . '/' . $zip_path);
                                    $count = 0;
                                    foreach ($zip_path_array as $zip_key => $zip_file) {
                                        if ($zip_key > 1) {
                                            $count++;
                                            if ($count == 1) {
                                                $html .= "<div>Mode: $file ($sub_folder)";
                                            }
                                            $html .=
                                                "<a href='/invoice/zip/download/$file/$sub_folder/zip/$zip_file'>
                                             <li class='ml-4'>Invoice Part " . $zip_key - 1 . ' ' . date("M-d-Y H:i:s.", filemtime("$sub_folder_path/$zip_path/$zip_file")) . "</li>
                                        </a>";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        if ($html == '') {
            return '<div> File Is Downloading....</div>';
        }
        $html .= '</div>';
        return $html;
    }

    public function zipDownloadLink($mode, $date, $file)
    {
        return Storage::download("invoice/$mode/$date/zip/$file");
    }


    public function DirectDownloadPdf(Request $request, $id)
    {
        $data = Invoice::where("invoice_no", "${id}")->get();
        $invoice_no = $data[0]->invoice_no;
        $mode = $data[0]->mode;

        $currenturl =  URL::current();
        $url = str_replace('download-direct', 'convert-pdf', $currenturl);
        $path = storage::path("invoice/$mode/invoice" . $invoice_no);
        $exportToPdf = $path . '.pdf';
        Browsershot::url($url)
            // ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
            ->showBackground()
            ->savePdf($exportToPdf);

        return $this->DownloadPdf($invoice_no, $mode);
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
            // ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
            ->showBackground()
            ->savePdf($exportToPdf);

        return response()->json(["success" => "Export to PDF Successfully"]);
    }

    public function DownloadPdf($invoice_no, $mode)
    {
        return Storage::download("invoice/$mode/invoice" . $invoice_no . '.pdf');
    }

    public function DownloadAll()
    {
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:excel-bulkpdf-download > /dev/null &";
            exec($command);
        } else {
            Artisan::call('pms:excel-bulkpdf-download');
        }
        $fileName = Storage::path('zip/' . 'invoice.zip');
        return response()->download($fileName);
    }

    public function downloadTemplate()
    {
        $filepath = public_path('template/Invoice-Template.csv');
        return Response()->download($filepath);
    }

    public function invoiceDataFormating($id, $type = 'bulk')
    {
        $invoice_details = [];
        $grand_total = 0;
        $invoice_no = $id;

        $ignore = explode(
            ',',
            trim(getSystemSettingsValue(
                'ignore_invoice_title_keys',
                'gun, lighter, gold, spark, Fuel, Heat, Oxygen, alcohols, flamable, seed, sliver, stone, leather, jewellery, fungicide, fertilizer, Magnet'
            ))
        );

        $prefix = config('database.connections.web.prefix');
        if ($type == 'bulk') {

            $data = Invoice::select('invoice_no')->whereIn('id', $id)->get();
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
            GROUP_CONCAT(DISTINCT ship_to_add) as ship_to_add,
            GROUP_CONCAT(DISTINCT ship_to_name) as ship_to_name,
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
            GROUP_CONCAT(client_code SEPARATOR '-invoice-') as clientcode
             from ${prefix}invoices where invoice_no IN ($invoice_no)
             group by invoice_no"
            );

        $item_details = [
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
        $item_details_tem[] = [];

        $item_details_final_array = [];
        $grand_total = 0;
        foreach ($invoice_data_array as $key => $value) {

            $grand_total = 0;
            foreach ($value as $key1 => $details) {

                if (array_key_exists($key1, $item_details)) {

                    $product_array = explode('-invoice-', $details);
                    if ($key1 == 'total_including_taxes') {

                        foreach ($product_array as $key2 => $val) {
                            $grand_total += (int) $val;
                        }
                    } elseif ($key1 == 'item_description') {
                        foreach ($product_array as $key2 => $val) {

                            $ignore_title = str_ireplace($ignore, '', $val);
                            $item_details_tem[$key2][$key1] = $ignore_title;
                        }
                    } else {
                        foreach ($product_array as $key2 => $val) {
                            $item_details_tem[$key2][$key1] = $val;
                        }
                    }
                } else {
                    $invoice_details[$key1] =  str_ireplace('-invoice-', '', $details);
                }
            }
            $invoice_details['grand_total'] = $grand_total;
            $invoice_details['product_details'] = $item_details_tem;
            $item_details_final_array[$key] = $invoice_details;
            $invoice_details = [];
            $item_details_tem = [];
        }

        return $item_details_final_array;
    }

    public function edit($id)
    {
        // $url = URL::previous();
        // $data = DB::connection('web')->select("SELECT * FROM invoices WHERE invoice_no = '$id' ");
        $data = Invoice::where("invoice_no", "${id}")->get();
        return view('invoice.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);
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

    public function InvoiceFileManagementMonitor(Request $request)
    {
        $type = $request->module_type;

        $file_check = fileManagementMonitoringNew($type);
        return response()->json($file_check);
    }

    public function UploadCsv()
    {
        return view('invoice.uploadCsv');
    }

    public function InvoiceCsvFileUpload(Request $request)
    {
        $request->validate([
            'invoice_csv' => 'required|mimes:txt,csv'
        ]);

        $import_file_time = date('Y-m-d-H-i-s');
        $file = $request->invoice_csv;
        $file_name = $file->getClientOriginalName();
        $path = "invoiceCSV/invoice${import_file_time}.csv";
        $file_data = file_get_contents($request->invoice_csv);
        Storage::put($path, $file_data);

        $user_id = Auth::user()->id;

        $file_info = [
            'user_id' => $user_id,
            'type' => 'IMPORT_INVOICE',
            'module' => 'INVOICE',
            'file_name' => $file_name,
            'file_path' => $path,
            'command_name' => 'mosh:invoice-csv-import',
        ];

        FileManagement::create($file_info);
        fileManagement();
        return redirect('invoice/upload/csv')->with('success', 'Invoice File has been uploaded, checking file\'s data');
    }
}
