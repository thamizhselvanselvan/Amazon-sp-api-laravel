<?php

namespace App\Http\Controllers\shipntrack\Operations\Label;


use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\ShipNTrack\Operation\Label\ShipNTrackLabel;
use App\Models\ShipNTrack\Operation\LabelMaster\LabelMaster;

class ShipnTrackLabelManagementController extends Controller
{
    protected $table_array = ["UAE" => "tracking_aes", "IND" => "tracking_ins", "KSA" => "tracking_ksa"];
    protected $model = ["UAE" => "Trackingae", "IND" => "Trackingin", "KSA" => "Trackingksa"];

    public function __construct()
    {
        $this->middleware('auth')->except('LabelPdfTemplateShow');
    }

    public function index(Request $request)
    {
        $values = LabelMaster::select('id', 'source', 'destination')
            ->groupBy('source', 'destination')
            ->orderBy('id', 'ASC')
            ->get()
            ->toArray();

        if ($request->ajax()) {

            $destination = $request->destination;
            $table_name = $this->table_array[$request->destination];

            $records = DB::connection('shipntracking')->select("SELECT
                     GROUP_CONCAT(DISTINCT id)as id,
                     GROUP_CONCAT(DISTINCT awb_no)as awb_no,
                     GROUP_CONCAT(DISTINCT consignee_details SEPARATOR '-customer-details-') as customer_details,
                     GROUP_CONCAT(DISTINCT shipping_details SEPARATOR '-shipping-details-') as shipping_details,
                     GROUP_CONCAT(DISTINCT booking_details SEPARATOR '-booking-details-') as booking_details,
                     purchase_tracking_id
                     FROM $table_name
                     GROUP BY purchase_tracking_id,awb_no
                     ");
            if (!empty($records)) {

                return DataTables::of($records)
                    ->addColumn('select_all', function ($record) {
                        return "<input class='check_options' type='checkbox' value='$record->id' name='options[]' >";
                    })
                    ->editColumn('order_no', function ($record) {

                        $split = preg_split("/-booking-details-?/", $record->booking_details);
                        $order_no = count($split) > 2 ? $split[1] : $split[0];
                        $order_no = json_decode($order_no);
                        return $order_no->order_id;
                    })
                    ->editColumn('awb_no', function ($record) {
                        return $record->awb_no;
                    })
                    ->editColumn('courier_name', function ($record) {

                        $split = preg_split("/-shipping-details-?/", $record->shipping_details);
                        $shipping_address = count($split) > 2 ? $split[1] : $split[0];
                        $courier_name = json_decode($shipping_address);
                        return $courier_name->shipped_by;
                    })
                    ->editColumn('order_date', function ($record) {

                        $split_date = preg_split("/-booking-details-?/", $record->booking_details);
                        $booked_date = count($split_date) > 2 ? $split_date[1] : $split_date[0];
                        $order_date = json_decode($booked_date);
                        return $order_date->booking_date;
                    })
                    ->editColumn('customer_name', function ($record) {

                        $split_customer = preg_split("/-customer-details-?/", $record->customer_details);
                        $customer_address = count($split_customer) > 2 ? $split_customer[1] : $split_customer[0];
                        $customer_name = json_decode($customer_address);
                        return $customer_name->consignee;
                    })
                    ->addColumn('action', function ($record) use ($destination) {
                        $id = $record->id;
                        $action = "<div class='d-flex justify-content-center'>
                                        <a href='/shipntrack/label/template/$destination/$id 'class='label_view btn btn-success btn-sm ml-2 mr-2' target='_blank'>
                                            <i class='fas fa-eye'></i> View 
                                        </a>
                                        <a href='/shipntrack/label/pdf/download/$destination/$id 'class='label_download btn btn-info btn-sm mr-2'>
                                        <i class='fas fa-download'></i> Download PDF </a>

                                    </div>";
                        return $action;
                    })
                    ->rawColumns(['select_all', 'order_no', 'awb_no', 'courier_name', 'order_date', 'customer_name', 'action'])
                    ->make(true);
            }
        }

        return view('shipntrack.Operation.LabelManagement.Label.index', compact('values'));
    }


    public function LabelPdfTemplateShow(Request $request)
    {
        $bar_code = [];
        $destination = $request->destination;
        $ids = implode(',', explode('-', $request->id));

        $data = $this->ShipntrackLabelDataFormatting($destination, $ids);
        $records = $this->LableDataFormattting($data);

        foreach ($records as $key => $record) {

            $generator = new BarcodeGeneratorPNG();
            $bar_code[] = base64_encode($generator->getBarcode($this->CleanupLabelData($record['awb_no'], 'awb'), $generator::TYPE_CODE_39));
        }

        return view('shipntrack.Operation.LabelManagement.Label.LabelPdfTemplate', compact('records', 'bar_code'));
    }

    public function LableDataFormattting($records)
    {
        $Label_data = [];
        foreach ($records as $key1 => $record) {

            foreach ($record as $key2 => $result) {

                if ($key2 == "shipping_details") {

                    $split_details = preg_split("/-shipping-details-?/", $result);
                    $shipping_details = $this->SplitJsonArray($split_details);
                    $Label_data[$key1]['forwarder'] = $shipping_details->shipped_by;

                    $shippings = explode("-shipping-details-", $result);
                    $sku = [];
                    foreach ($shippings as $data) {
                        $sku[] =  json_decode($data)->sku ?? '';
                    }

                    $Label_data[$key1]['sku'] = array_unique($sku);
                } elseif ($key2 == "order_details") {

                    $split_order = preg_split("/-order-details-?/", $result);
                    $order_details = $this->SplitJsonArray($split_order);
                    $Label_data[$key1]['order_no'] = $order_details->order_id;
                    $Label_data[$key1]['order_date'] = $order_details->booking_date;
                } elseif ($key2 == "customer_details") {

                    $split_customer = preg_split("/-customer-details-?/", $result);
                    $customer_details = $this->SplitJsonArray($split_customer);

                    $Label_data[$key1]['customer_name'] = $customer_details->consignee;
                    $Label_data[$key1]['address'] = $customer_details->address1 . " , " . $customer_details->address2;
                    $Label_data[$key1]['city'] = $customer_details->city;
                    $Label_data[$key1]['country'] = $customer_details->country;
                    $Label_data[$key1]['phone'] = $customer_details->mobile_no;
                } elseif ($key2 == "product_details") {

                    $packet_details = explode("-product-details-", $result);
                    foreach ($packet_details as $packet_detail) {

                        $Label_data[$key1]['product_name'][] = json_decode($packet_detail)->pkt_name;
                        $Label_data[$key1]['quantity'][] = json_decode($packet_detail)->quantity;
                    }
                } elseif ($key2 == 'awb_no') {
                    $Label_data[$key1][$key2] = $result;
                }
            }
        }
        return $Label_data;
    }

    public function SplitJsonArray($data)
    {
        $split_json = count($data) > 2 ? $data[1] : $data[0];
        $result = json_decode($split_json);
        return $result;
    }

    public function ShipntrackLabelDataFormatting($destination, $label_id)
    {
        $table_name = $this->table_array[$destination];

        $records = DB::connection('shipntracking')->select("SELECT 
                        
                        GROUP_CONCAT(DISTINCT awb_no) as awb_no,
                        GROUP_CONCAT(consignee_details SEPARATOR '-customer-details-') as customer_details,
                        GROUP_CONCAT(packet_details SEPARATOR '-product-details-') as product_details,
                        GROUP_CONCAT(shipping_details SEPARATOR '-shipping-details-') as shipping_details,
                        GROUP_CONCAT(booking_details SEPARATOR '-order-details-') as order_details,
                        GROUP_CONCAT(DISTINCT purchase_tracking_id) as purchase_tracking_id
                        FROM  $table_name
                        WHERE id IN($label_id)
                        GROUP BY purchase_tracking_id
                        ");

        return $records;
    }

    public function LabelPdfDownload($destination, $id)
    {
        $awbNo = '';
        $current_url = URL::current();
        $url = str_replace('pdf/download', 'template', $current_url);

        if (str_contains($id, "-")) {
            $awbNo = $destination;
        } else {

            $table = $this->table_array[$destination];
            $table_object = table_model_change(model_path: "ForwarderMaping", model_name: $this->model[$destination], table_name: $table);
            $label_record = $table_object->where('id', $id)->get('purchase_tracking_id')->toArray();
            $awbNo = $label_record[0]['purchase_tracking_id'];
        }

        $filePath = "shipntrack/label/$awbNo.pdf";
        if (!Storage::exists($filePath)) {
            Storage::put($filePath, '');
        }
        $pdfPath = Storage::path($filePath);

        Browsershot::url($url)
            ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
            ->paperSize(576, 384, 'px')
            ->pages('1-40')
            ->scale(1)
            ->margins(0, 0, 0, 0)
            ->savePdf($pdfPath);

        return Storage::download($filePath);
    }

    public function CleanupLabelData($data, $type)
    {
        if ($type == 'awb') {
            return preg_replace("/[^a-zA-Z0-9]/", "", strtoupper($data));
        }
        return $data;
    }
}
