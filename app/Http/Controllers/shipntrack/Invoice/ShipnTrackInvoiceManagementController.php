<?php

namespace App\Http\Controllers\shipntrack\Invoice;

use Nette\Utils\Json;
use RedBeanPHP\Util\Tree;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Picqer\Barcode\BarcodeGeneratorHTML;
use App\Models\ShipNtrack\Process\Process_Master;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;

class ShipnTrackInvoiceManagementController extends Controller
{
    public function index(Request $request)
    {

        $values = Process_Master::select('source', 'destination')
            ->groupBy('source', 'destination')
            ->get()
            ->toArray();

        if ($request->ajax()) {

            $destination = '';
            if ($request->destination == 'SA') {
                $destination = 'KSA';
            } else {

                $destination = $request->destination;
            }

            $table = table_model_change(model_path: 'ForwarderMaping', model_name: 'Tracking' . strtolower($destination), table_name: 'tracking' . strtolower($destination) . 's');

            $invoice_data = $table->query()
                ->select(['id', 'awb_no', 'packet_details', 'booking_details', 'shipping_details', 'packet_details'])
                ->get();

            if (!empty($invoice_data)) {

                return DataTables::of($invoice_data)
                    ->addColumn('select_all', function ($result) {

                        return "<input type='checkbox' name='all[]' value='$result->id' class='check_options'> ";
                    })
                    ->addColumn('invoice_no', function ($result) {
                        $invoice_no = json_decode($result->packet_details)->invoice_no;
                        return $invoice_no;
                    })
                    ->addColumn('invoice_date', function ($result) {
                        $invoice_date = json_decode($result->booking_details)->booking_date;
                        return $invoice_date;
                    })
                    ->addColumn('channel', function ($result) {
                        $channel = json_decode($result->shipping_details)->shipping_channel;
                        return $channel;
                    })
                    ->addColumn('shipped_by', function ($result) {
                        $shipped_by = json_decode($result->shipping_details)->shipped_by;
                        return $shipped_by;
                    })
                    ->addColumn('store_name', function ($result) {
                        $store_name = json_decode($result->shipping_details)->store;
                        return $store_name;
                    })
                    ->addColumn('bill_to_name', function ($result) {
                        $bill_to_name = json_decode($result->shipping_details)->bill_to_name;
                        return $bill_to_name;
                    })
                    ->addColumn('ship_to_name', function ($result) {
                        $ship_to_name = json_decode($result->shipping_details)->ship_to_name;
                        return $ship_to_name;
                    })
                    ->addColumn('sku', function ($result) {
                        $sku = json_decode($result->shipping_details)->sku;
                        return $sku;
                    })
                    ->addColumn('quantity', function ($result) {
                        $quantity = json_decode($result->packet_details)->quantity;
                        return $quantity;
                    })
                    ->addColumn('price', function ($result) {
                        $price = json_decode($result->packet_details)->price;
                        return $price;
                    })
                    ->addColumn('action', function ($result) use ($destination) {
                        $action = "<div class='d-flex justify-content-center'>
                                        <a href='/shipntrack/invoice/template/$destination/$result->id 'class='label_view btn btn-success btn-sm ml-2 mr-2' target='_blank'>
                                            <i class='fas fa-eye'></i> View 
                                        </a>
                                        <a href='/shipntrack/invoice/download/$destination/$result->id 'class='label_download btn btn-info btn-sm mr-2'>
                                        <i class='fas fa-download'></i> Download PDF </a>

                                    </div>";
                        return $action;
                    })

                    ->rawColumns(['select_all', 'invoice_no', 'invoice_date', 'channel', 'shipped_by', 'store_name', 'bill_to_name', 'ship_to_name', 'sku', 'quantity', 'price', 'action'])
                    ->make(true);
            }
        }

        return view('shipntrack.Invoice.index', compact('values'));
    }

    public function SNTInvoiceTemplate($destination, $id)
    {
        $ids =   explode('-', $id);
        $data = $this->ShipnTrackInvoiceData($destination, $ids);
        $records = $this->ShipnTrackInvoiceDataFormatting($data);

        po($records);

        $invoice_bar_code = [];
        $generator = new BarcodeGeneratorHTML();
        foreach ($records as $key => $record) {

            $invoice_bar_code[] = $generator->getBarcode($records[$key]['invoice_no'], $generator::TYPE_CODE_128);
        }

        return view('shipntrack.Invoice.ind2uae', compact('records', 'invoice_bar_code'));
    }

    public function ShipnTrackInvoiceData($destination, $ids)
    {

        if ($destination == 'SA') {
            $destination = 'KSA';
        }
        $table = table_model_change(model_path: 'ForwarderMaping', model_name: 'Tracking' . strtolower($destination), table_name: 'tracking' . strtolower($destination) . 's');
        $records = $table->query()
            ->select(['awb_no', 'packet_details', 'booking_details', 'shipping_details', 'packet_details'])
            ->whereIn('id', ($ids))
            ->get()
            ->toArray();

        return $records;
    }

    public function ShipnTrackInvoiceDataFormatting($records)
    {
        $invoice_records = [];
        foreach ($records as $key1 => $record) {

            $invoice_date = json_decode($record['booking_details'])->booking_date;

            $packet_details = json_decode($record['packet_details']);
            $invoice_no = $packet_details->invoice_no;
            $packet_desc = $packet_details->pkt_name;
            $quantity = $packet_details->quantity;
            $currency = $packet_details->currency;
            $price = $packet_details->price;
            $tax = $packet_details->tax_value;
            $grand_total = $packet_details->total_amount;

            $shipping_details = json_decode($record['shipping_details']);

            $channel = $shipping_details->shipping_channel;
            $shipped_by = $shipping_details->shipped_by;
            $arn_no = $shipping_details->arn_no;

            $store_name = $shipping_details->store;
            $store_add = $shipping_details->store_address;

            $bill_to_name = $shipping_details->bill_to_name;
            $bill_to_add = $shipping_details->billing_address;

            $ship_to_name = $shipping_details->ship_to_name;
            $ship_to_add = $shipping_details->shipping_address;
            $hsn_code = $shipping_details->hsn;


            $invoice_records[$key1] = [
                'awb_no' => $record['awb_no'],
                'invoice_date' => $invoice_date,
                'invoice_no' => $invoice_no,
                'channel' => $channel,
                'shipped_by' => $shipped_by,
                'arn_no' => $arn_no,
                'store_name' => $store_name,
                'store_add' => $store_add,
                'bill_to_name' => $bill_to_name,
                'bill_to_add' => $bill_to_add,
                'ship_to_name' => $ship_to_name,
                'ship_to_add' => $ship_to_add,
            ];

            $invoice_records[$key1]['product_details'][0] = [

                'item_description' => $packet_desc,
                'hsn_code' => $hsn_code,
                'quantity' => $quantity,
                'product_price' => $price,
                'currency' => $currency,
                'taxable_value' => $tax,
                'grand_total' => $grand_total
            ];
        }

        return $invoice_records;
    }
}
