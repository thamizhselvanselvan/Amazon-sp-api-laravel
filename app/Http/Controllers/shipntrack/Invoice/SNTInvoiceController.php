<?php

namespace App\Http\Controllers\shipntrack\Invoice;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\Invoice\SNTInvoice;

class SNTInvoiceController extends Controller
{
    public function __construct()
    {

        $this->middleware('auth')->except('invoiceview');
    }
    // Invoice Index
    public function Index(Request $request)
    {

        $modes = SNTInvoice::select('mode')->distinct()->get();
        $request_mode = $request->mode;
        $url = "/shipntrack/invoice";

        if (isset($request_mode)) {
            $url = "/shipntrack/invoice/" . $request_mode;
        }
        if ($request->ajax()) {

            $data = SNTInvoice::query()
                ->when($request->mode, function ($query, $role) use ($request) {
                    return $query->where('mode', $role);
                })
                ->orderBy('created_at', 'DESC')
                ->limit(100)->get();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $action_btn =
                        "<div class='d-flex'>
                            <a href='/shipntrack/invoice/view/$row->invoice_no' class='edit btn btn-success btn-sm ml-2 mr-2' target='_blank'>
                                <i class='fas fa-eye'></i> View 
                            </a>
                            <a href='/shipntrack/invoice/download-direct/$row->invoice_no' class='edit btn btn-info btn-sm mr-2'>
                                <i class='fas fa-download'></i> Download
                            </a>
                            <a href='/shipntrack/invoice/edit/$row->invoice_no' class='edit btn btn-primary btn-sm mr-2'>
                                <i class='fas fa-edit'></i> Edit 
                            </a>
                        </div>";
                    return $action_btn;
                })
                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('shipntrack.Invoice.index', compact('modes', 'url', 'request_mode'));
    }

    //Invoice Create View
    public function create(Request $request)
    {

        return view('shipntrack.Invoice.create');
    }

    // Invoice Store data
    public function store(Request $request)
    {
        $request->validate([
            'invoice_no' => 'required',
            'awb_no' => 'required',
            'mode' => 'required |in:IND2UAE,USA2UAE',
            'invoice_date' => 'required',
            'sku' => 'required',
            'channel' => 'required',
            'shipped_by' => 'required',
            'arn_no' => 'required',
            'store_name' => 'required',
            'store_add' => 'required',
            'bill_to_name' => 'required',
            'bill_to_add' => 'required',
            'ship_to_name' => 'required',
            'ship_to_add' => 'required',
            'item_description' => 'required',
            'hsn_code' => 'required',
            'quantity' => 'required',
            'currency' => 'required',
            'product_price' => 'required',
            'taxable_value' => 'required',
            'total_including_taxes' => 'required',
            'grand_total' => 'required',
            'packing' => 'required',
            // 'no_of_pcs' => 'required',
            // 'dimension' => 'required',
            // 'actual_weight' => 'required',
            // 'charged_weight' => 'required',
            'sr_no' => 'required',
            'client_code' => 'required',
        ]);

        $insert_data = [
            'invoice_no' => $request->invoice_no,
            'awb_no' => $request->awb_no,
            'mode' => strtoupper($request->mode),
            'invoice_date' => $request->invoice_date,
            'sku' => $request->sku,
            'channel' => $request->channel,
            'shipped_by' => $request->shipped_by,
            'arn_no' => $request->arn_no,
            'store_name' => $request->store_name,
            'store_add' => $request->store_add,
            'bill_to_name' => $request->bill_to_name,
            'bill_to_add' => $request->bill_to_add,
            'ship_to_name' => $request->ship_to_name,
            'ship_to_add' => $request->ship_to_add,
            'item_description' => $request->item_description,
            'hsn_code' => $request->hsn_code,
            'quantity' => $request->quantity,
            'currency' => $request->currency,
            'product_price' => $request->product_price,
            'taxable_value' => $request->taxable_value,
            'total_including_taxes' => $request->total_including_taxes,
            'grand_total' => $request->grand_total,
            'no_of_pcs' => $request->no_of_pcs,
            'packing' => $request->packing,
            'dimension' => $request->dimension,
            'actual_weight' => $request->actual_weight,
            'charged_weight' => $request->charged_weight,
            'sr_no' => $request->sr_no,
            'client_code' => $request->client_code,
        ];
        SNTInvoice::upsert(
            $insert_data,
            ['invoice_no_sku_unique'],
            ['invoice_date', 'awb_no', 'item_description', 'quantity', 'grand_total', 'client_code']
        );

        return redirect()->route('shipntrack.invoice')->with('success', 'Invoice  has been created successfully');
    }

    //Invoice View
    public function invoiceview(Request $request)
    {
        $id = $request->invoice_no;

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
            return view('shipntrack.Invoice.' . $invoice_mode, compact(['value'], 'invoice_no', 'invoice_bar_code', 'bar_code'));
        }
    }

    //Formatting Incoice Data
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

        if ($type == 'bulk') {

            $data = SNTInvoice::select('invoice_no')->whereIn('id', $id)->get();
            $invoice_no = [];
            foreach ($data as $key => $value) {
                $invoice_no[] = "$value->invoice_no";
            }
            $invoice_no =  "'" . implode("','", $invoice_no) . "'";
        }

        $invoice_data_array = DB::connection('shipntracking')
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
            GROUP_CONCAT(quantity SEPARATOR '-invoice-') as qty,
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
             from invoices where invoice_no IN ($invoice_no)
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

    //Single View Pdf Download
    public function pdfexport(Request $request)
    {
        $this->deleteAllPdf();
        $id = $request->invoice_no;
        $url = $request->url;
        $file_path =  'shipntrack/invoice/sntinvoice' . $id . '.pdf';
        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }
        $exportToPdf = storage::path($file_path);
        Browsershot::url($url)
            // ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
            ->showBackground()
            ->savePdf($exportToPdf);

        return response()->json(["success" => "Export to PDF Successfully"]);
    }

    //direct PDF Download
    public function directpdfdownload(Request $request, $id)
    {
        $this->deleteAllPdf();
        $data = SNTInvoice::where("invoice_no", "{$id}")->get();
        $invoice_no = $data[0]->invoice_no;

        $currenturl =  URL::current();
        $url = str_replace('download-direct', 'view', $currenturl);

        $exportToPdf = storage::path("shipntrack/invoice/sntinvoice" . $invoice_no . '.pdf');
        Browsershot::url($url)
            // ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
            ->showBackground()
            ->savePdf($exportToPdf);

        return  $this->sntDownloadPdf($invoice_no);
    }

    //download Pdf Local
    public function sntDownloadPdf($invoice_no)
    {
        return Storage::download("shipntrack/invoice/sntinvoice" . $invoice_no . '.pdf');
    }

    //SNT Delete Old PDF's
    public function deleteAllPdf()
    {
        $files = glob(Storage::path('shipntrack/invoice/*'));
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }


    //SNT Invoiced EDIT
    public function invoiceeditview($invoice_number)
    {
        $data = SNTInvoice::query()
            ->where('invoice_no', $invoice_number)
            ->limit(100)->get()->first();

        $mode = (strtoupper($data->mode));
        return view('shipntrack.Invoice.edit', compact('data', 'mode'));
    }

    //SNT Invoiced EDIT SAVE
    public function invoiceeditstore(Request $request)
    {
        $request->validate([
            'invoice_no' => 'required',
            'awb_no' => 'required',
            'mode' => 'required',
            'invoice_date' => 'required',
            'sku' => 'required',
            'channel' => 'required',
            'shipped_by' => 'required',
            'arn_no' => 'required',
            'store_name' => 'required',
            'store_add' => 'required',
            'bill_to_name' => 'required',
            'bill_to_add' => 'required',
            'ship_to_name' => 'required',
            'ship_to_add' => 'required',
            'item_description' => 'required',
            'hsn_code' => 'required',
            'quantity' => 'required',
            'currency' => 'required',
            'product_price' => 'required',
            'taxable_value' => 'required',
            'total_including_taxes' => 'required',
            'grand_total' => 'required',
            'no_of_pcs' => 'required',
            'packing' => 'required',
            'dimension' => 'required',
            'actual_weight' => 'required',
            'charged_weight' => 'required',
            'sr_no' => 'required',
            'client_code' => 'required',
        ]);
        $update_data = [
            'invoice_no' => $request->invoice_no,
            'awb_no' => $request->awb_no,
            'mode' => strtoupper($request->mode),
            'invoice_date' => $request->invoice_date,
            'sku' => $request->sku,
            'channel' => $request->channel,
            'shipped_by' => $request->shipped_by,
            'arn_no' => $request->arn_no,
            'store_name' => $request->store_name,
            'store_add' => $request->store_add,
            'bill_to_name' => $request->bill_to_name,
            'bill_to_add' => $request->bill_to_add,
            'ship_to_name' => $request->ship_to_name,
            'ship_to_add' => $request->ship_to_add,
            'item_description' => $request->item_description,
            'hsn_code' => $request->hsn_code,
            'quantity' => $request->quantity,
            'currency' => $request->currency,
            'product_price' => $request->product_price,
            'taxable_value' => $request->taxable_value,
            'total_including_taxes' => $request->total_including_taxes,
            'grand_total' => $request->grand_total,
            // 'no_of_pcs' => $request->no_of_pcs,
            'packing' => $request->packing,
            // 'dimension' => $request->dimension,
            // 'actual_weight' => $request->actual_weight,
            // 'charged_weight' => $request->charged_weight,
            'sr_no' => $request->sr_no,
            'client_code' => $request->client_code,
        ];
        SNTInvoice::where('invoice_no', $request->invoice_no)->update($update_data);

        return redirect()->route('shipntrack.invoice')->with('success', 'Invoice  has been Updated successfully');
    }
}
