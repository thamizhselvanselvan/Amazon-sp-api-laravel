<?php

namespace App\Http\Controllers\label;

use DateTime;
use Exception;
use App\Models;
use ZipArchive;
use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\Label;
use League\Csv\Reader;
use League\Csv\Writer;
use App\Models\Mws_region;
use Illuminate\Http\Request;
use App\Jobs\Orders\GetOrder;
use App\Models\FileManagement;
use App\Models\GoogleTranslate;
use Yajra\DataTables\DataTables;
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
use Picqer\Barcode\BarcodeGeneratorPNG;
use Illuminate\Support\Facades\Validator;
use App\Models\order\OrderSellerCredentials;
use App\Services\SP_API\API\Order\OrderUsingRedBean;

class labelManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('showTemplate');
    }

    private $order_details;
    public function SearchLabel(Request $request)
    {
        if ($request->ajax()) {
            $currentPageNumber = $request->start / $request->length + 1;

            $bag_no = $request->bag_no;
            $data = $this->labelListing($bag_no);

            return DataTables::of($data)
                ->addColumn('select_all', function ($data) use ($currentPageNumber) {
                    $name = json_decode($this->lableDataCleanup($data->shipping_address, 'address'));
                    if (isset($name->Name)) {
                        return "<input class='check_options' type='checkbox' value='$data->id' data-current-page='$currentPageNumber' name='options[]' id='checkid$data->id'>";
                    }
                })
                ->addColumn('name', function ($data) {
                    $name = json_decode($this->lableDataCleanup($data->shipping_address, 'address'));
                    if (isset($name->Name)) {
                        return $name->Name;
                    }
                    return 'NA';
                })
                ->addColumn('action', function ($data) use ($bag_no) {
                    $table = '';
                    $name = json_decode($this->lableDataCleanup($data->shipping_address, 'address'));
                    if (isset($name->Name)) {
                        $table .=
                            "<div class='d-flex'>
                            <a href='/label/pdf-template/$bag_no-$data->id' class='edit btn btn-success btn-sm ml-2 mr-2' target='_blank'>
                                <i class='fas fa-eye'></i> View 
                            </a>
                            <a href='/label/download-direct/$bag_no-$data->id' class='edit btn btn-info btn-sm mr-2'>
                            <i class='fas fa-download'></i> Download </a>";
                    }
                    $table .=
                        "<a id='edit-address' data-toggle='modal' data-id='$data->order_item_identifier' data-amazon_order_identifier='$data->order_no ' href='javascript:void(0)' class='edit btn btn-secondary btn-sm'>
                        <i class='fas fa-address-card'></i> Address </a>
                        </div>";
                    return $table;
                })

                ->rawColumns(['select_all', 'action'])
                ->make(true);
        }
        return view('label.search_label');
    }

    public function manage(Request $request)
    {
        return view('label.manage');
    }

    public function downloadExcelTemplate()
    {
        $filepath = public_path('template/Label-Template.csv');
        return Response()->download($filepath);
    }

    public function upload()
    {
        return  view('label.upload');
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'label_csv_file' => 'required|mimes:txt,csv'
        ]);

        $path = "label/label.csv";
        $file_data = file_get_contents($request->label_csv_file);
        Storage::put($path, $file_data);

        $records = Reader::createFromPath(Storage::path($path), 'r');
        $records->setHeaderOffset(0);

        $label_csv_data = [];
        foreach ($records as $value) {

            $label_csv_data[] = [
                "order_no" => $value['OrderNo'],
                "order_item_id" => $value['OrderItemId'],
                "awb_no" => $value['OutwardAwb'],
                "inward_awb" => $value['InwardAwb'],
                "bag_no" => $value['BagNo'],
                "forwarder" => $value['Forwarder']
            ];
        }

        Label::upsert(
            $label_csv_data,
            ['order_item_bag_unique'],
            ['order_no', 'awb_no', 'inward_awb', 'bag_no', 'forwarder']
        );

        commandExecFunc("mosh:detect-arabic-language-into-label");

        return redirect('label/upload')->with('success', 'Label File has been uploaded, checking file\'s data');
    }

    public function showTemplate($id)
    {
        $id_array = explode('-', $id);
        if (isset($id_array[0]) && isset($id_array[1])) {
            $bag_no = $id_array[0];
            $table_id = $id_array[1];
            $result = $this->labelDataFormating("'$table_id'");

            $getTranslatedText = $this->GetArabicToEnglisText($result);

            $result = $result[0];
            $awb_no = $result['awb_no'];
            $forwarder = $result['forwarder'];

            if ($awb_no == '' || $awb_no == NULL) {
                $awb_no = 'AWB-MISSING';
            }
            $result = (object)$result;

            $generator = new BarcodeGeneratorPNG();
            $bar_code = base64_encode($generator->getBarcode($this->lableDataCleanup($awb_no, 'awb'), $generator::TYPE_CODE_39));
            return view('label.labelTemplate', compact('result', 'bar_code', 'awb_no', 'forwarder', 'bag_no', 'getTranslatedText'));
        }
    }

    public function ExportLabel(Request $request)
    {
        //Single Download
        $this->deleteAllPdf();
        $url = $request->url;
        $awb_no = $request->awb_no;
        $bag_no = $request->bag_no;

        $file_path = "label/$bag_no/label$awb_no.pdf";

        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }
        $exportToPdf = storage::path($file_path);
        Browsershot::url($url)
            // ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
            ->paperSize(576, 384, 'px')
            ->pages('1')
            ->scale(1)
            ->margins(0, 0, 0, 0)
            ->savePdf($exportToPdf);

        return response()->json(['Save pdf sucessfully']);
    }

    public function downloadLabel($bag_no, $awb_no)
    {
        return Storage::download("label/$bag_no/label$awb_no.pdf");
    }

    public function DownloadDirect($id)
    {
        $this->deleteAllPdf();

        $id_array = explode('-', $id);
        $id = $id_array[1];
        $bag_no = $id_array[0];

        $result = Label::where('id', $id)->get();
        $awb_no = $result[0]->awb_no;

        $file_path = "label/$bag_no/label$awb_no.pdf";

        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }
        $exportToPdf = storage::path($file_path);
        $currentUrl = URL::current();
        $url = str_replace('download-direct', 'pdf-template', $currentUrl);

        Browsershot::url($url)
            // ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
            ->paperSize(576, 384, 'px')
            ->pages('1')
            ->scale(1)
            ->margins(0, 0, 0, 0)
            ->savePdf($exportToPdf);

        return $this->downloadLabel($bag_no, $awb_no);
    }

    public function PrintSelected($id)
    {
        $all_id_string = "'" . implode("','", explode('-', $id)) . "'";
        $results = $this->labelDataFormating($all_id_string);
        $getTranslatedText = $this->GetArabicToEnglisText($results);
        $generator = new BarcodeGeneratorPNG();

        $result = [];
        $bar_code = [];

        foreach ($results as $value) {

            $barcode_awb = 'AWB-MISSING';
            try {

                $result[] = (object)$value;
                if (($value['awb_no'])) {
                    $barcode_awb = $value['awb_no'];
                }

                $bar_code[] = base64_encode($generator->getBarcode($this->lableDataCleanup($barcode_awb, 'awb'), $generator::TYPE_CODE_39));
            } catch (Exception $e) {

                $getMessage = $e->getMessage();
                $getCode = $e->getCode();
                $getFile = $e->getFile();

                $slackMessage = "Message: $getMessage
                Code: $getCode
                File: $getFile
                Awb_No: $barcode_awb";

                slack_notification('app360', 'Label Bar Code Error', $slackMessage);
            }
        }

        return view('label.multipleLabel', compact('result', 'bar_code', 'getTranslatedText'));
    }

    public function DownloadSelected(Request $request)
    {
        $replace = [' - ', '-'];
        $passid = $request->id;
        $bag_no = $request->bag_no;
        $date = 'dayByday' . str_replace($replace, '+', $request->date);

        $current_page_number = $request->current_page_number;
        $bagNo_date = $bag_no == '' ? $date : $bag_no;

        $currenturl =  URL::current();
        $user_id = '';

        if (Auth::user()) {
            $user_id = Auth::user()->id;
        }

        $header = ["data" => "${passid}_${currenturl}_${bagNo_date}_${current_page_number}"];
        $file_info = [
            "user_id"       => $user_id,
            "type"          => "EXPORT_LABEL",
            "module"        => "LABEL_EXPORT",
            "command_name"  => "pms:label-bulk-zip-download",
            "header"        => json_encode($header)

        ];
        FileManagement::create($file_info);
        fileManagement();

        return response()->json(['success' => 'Zip created successfully']);
    }

    public function zipDownload()
    {
        $html = '';
        $html_final = '';
        $count = 0;
        $path = (Storage::path("label"));
        $files = scandir($path);
        foreach ($files as $key => $file) {
            if (!str_contains($file, 'dayByday')) {

                if ($key > 1) {
                    $file_path = Storage::path('label' . '/' . $file);
                    if (is_dir($file_path)) {
                        $file_paths = scandir($file_path);
                        foreach ($file_paths as $zip_path) {
                            if ($zip_path == 'zip') {
                                $zip_path_array = scandir($file_path . '/' . $zip_path);
                                $count = 0;
                                foreach ($zip_path_array as $zip_key => $zip_file) {
                                    if ($zip_key > 1) {
                                        $count++;
                                        if ($count == 1) {
                                            $html .= "<div>Bag No: $file";
                                        }
                                        $html .=
                                            "<a href='/label/zip/download/$file/zip/$zip_file'>
                                                 <li class='ml-4'>Label Part " . $zip_key - 1 . ' ' . date("M-d-Y H:i:s.", filemtime("$file_path/$zip_path/$zip_file")) . "</li>
                                            </a>";
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

    public function zipDownloadLink($bag_no, $file_name)
    {
        $path = "label/$bag_no/zip/$file_name";
        return Storage::download($path);
    }

    public function missing()
    {
        $selected_store = OrderSellerCredentials::where('dump_order', '1')
            ->where('cred_status', '1')
            ->get(['seller_id', 'store_name', 'country_code', 'source']);

        return view('label.missing', compact('selected_store'));
    }

    public function missingOrderId(Request $request)
    {
        $seller = explode(',', $request->seller_id);
        $order_id = $request->order_id;
        $seller_id = $seller[0];
        $store_name = $seller[1];
        $country_code = $seller[2];
        $source = $seller[3];

        $datas = preg_split('/[\r\n| |:|,]/', $order_id, -1, PREG_SPLIT_NO_EMPTY);

        $max_order  = 50;
        $order_count = 0;
        $order_array = [];
        $auth_code = NULL;

        foreach ($datas as $amazon_order_id) {

            $order_array[] = $amazon_order_id;
            $order_count++;
            if ($order_count == $max_order) {

                (new OrderUsingRedBean())->SelectedSellerOrder($seller_id, $country_code, $source, $auth_code, $order_array, $store_name);

                $order_count = 0;
                $order_array = [];
            }
        }

        if ($order_array) {
            (new OrderUsingRedBean())->SelectedSellerOrder($seller_id, $country_code, $source, $auth_code, $order_array, $store_name);
        }

        return redirect('/label/manage')->with("success", "Order Details Is Updating, Please Wait.");
    }

    public function bladeOrderDetails()
    {
        $data = '';
        $order = config('database.connections.order.database');
        $catalog = config('database.connections.catalog.database');
        $web = config('database.connections.web.database');
        $prefix = config('database.connections.web.prefix');

        $data = DB::select("SELECT

            DISTINCT web.id, web.awb_no, web.order_no, ord.purchase_date, store.store_name, orderDetails.seller_sku, orderDetails.shipping_address
            from ${web}.${prefix}labels as web
            JOIN ${order}.orders as ord ON ord.amazon_order_identifier = web.order_no
            JOIN ${order}.orderitemdetails as orderDetails ON orderDetails.amazon_order_identifier = web.order_no
            JOIN ${order}.order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id

            -- JOIN ord ON ord.our_seller_identifier = $order.ord_order_seller_credentials.seller_id as
        ");
        // exit;
        return $data;
    }

    public function deleteAllPdf()
    {
        $files = glob(Storage::path('label/*'));
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function labelMissingAddress()
    {
        return view('label.upload_missing_addrs');
    }

    public function labelMissingAddressUpload(Request $request)
    {
        foreach ($request->files as $key => $files) {

            foreach ($files as $keys => $file) {

                $fileName = $file->getClientOriginalName();
                $fileName = uniqid() . ($fileName);
            }
        }
        $file_csv = file_get_contents($file);
        $path = 'label/missing_address.csv';
        Storage::put($path, $file_csv);

        commandExecFunc("mosh:order-address-missing-upload");
        return response()->json(["success" => "All file uploaded successfully"]);
    }

    public function labelMissingAddressExport()
    {
        $missing_address = DB::connection('order')
            ->select(
                "SELECT 
                    osc.store_name, ord.purchase_date, oids.amazon_order_identifier, osc.country_code
                FROM 
                    orderitemdetails oids
                        JOIN
                    order_seller_credentials osc ON osc.seller_id = oids.seller_identifier
                        JOIN
                    orders ord oN ord.amazon_order_identifier = oids.amazon_order_identifier
                WHERE
                    oids.shipping_address = '' AND oids.amazon_order_identifier != '' "
            );

        $path = 'excel/downloads/label/missing_address_template.csv';
        $file_path = Storage::path('excel/downloads/label/missing_address_template.csv');

        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }

        $csv = Writer::createFromPath($file_path, 'w');
        $csv->insertOne([
            'Order',
            'Store Name',
            'Order Date',
            'Name',
            'AddressLine1',
            'AddressLine2',
            'City',
            'County',
            'CountryCode',
            'Phone',
            'AddressType'
        ]);

        foreach ($missing_address as $details) {

            $date = $details->purchase_date;
            $date = Carbon::parse($date)->format('Y-m-d');
            $tem_data = [
                $details->amazon_order_identifier,
                $details->store_name . ' [ ' . $details->country_code . ' ]',
                $date
            ];
            $csv->insertOne($tem_data);
        }

        return response()->download($file_path);
    }

    public function labelListing($id, $search_type = NULL)
    {
        if ($search_type == 'Amazon Order Id') {

            $where_condition = "web.order_no IN ($id)";
        } elseif ($search_type == 'Outward Awb No') {

            $where_condition = "web.awb_no IN ($id)";
        } elseif ($search_type == 'Inward Awb No') {

            $where_condition = "web.inward_awb IN ($id)";
        } else {

            $where_condition = "web.bag_no = '${id}' ";
        }
        if ($search_type == 'search_date') {
            $val = json_decode($id);
            $start_date = $val->start;
            $end_date = $val->end;
            $v1 = $start_date . " 00:00:01";
            $v2 = $end_date . " 23:59:59";
            $where_condition = "web.created_at BETWEEN '$v1' AND '$v2' ";
        }

        $order = config('database.connections.order.database');
        $web = config('database.connections.web.database');
        $prefix = config('database.connections.web.prefix');

        $data = DB::select("SELECT
            DISTINCT
            GROUP_CONCAT(DISTINCT web.id)as id, 
            GROUP_CONCAT(DISTINCT web.awb_no)as awb_no, 
            GROUP_CONCAT(DISTINCT web.forwarder)as forwarder,
             orderDetails.amazon_order_identifier as order_no,
             GROUP_CONCAT(DISTINCT ord.purchase_date)as purchase_date,
             GROUP_CONCAT(DISTINCT store.store_name)as store_name, 
             GROUP_CONCAT(DISTINCT orderDetails.seller_sku)as seller_sku, 
             orderDetails.shipping_address,
             GROUP_CONCAT(DISTINCT orderDetails.order_item_identifier)as order_item_identifier
            from ${web}.${prefix}labels as web
            JOIN ${order}.orders as ord ON ord.amazon_order_identifier = web.order_no
            JOIN ${order}.orderitemdetails as orderDetails ON orderDetails.amazon_order_identifier = web.order_no
            JOIN ${order}.order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id
            WHERE $where_condition
            group by orderDetails.amazon_order_identifier,orderDetails.shipping_address
            order by orderDetails.shipping_address
        ");
        // po($data);
        return $data;
    }

    public function trackingDetailsMissing($amazon_order_id)
    {
        $missing_html = '';
        foreach ($amazon_order_id as $order_id) {

            $missing_html .=
                "<tr> 
                    <td>$order_id</td>
                    <td><input type ='text' placeholder='Tracking Id' id ='tracking$order_id'> </td>
                    <td><input type ='text' placeholder ='Courier Forwarder' id='courier$order_id'> </td>
                    <td>
                        <div class='d-flex'>
                            <a id='$order_id' class='update btn btn-success btn-sm'>
                                <i class='fas fa-upload'></i> Update
                            </a>
                        </div>
                    <td>
                </tr>";
        }
        return $missing_html;
    }

    public function updateTrackingDetails(Request $request)
    {
        $order_id = $request->order_id;
        $tracking_id = $request->tracking_id;
        $courier = $request->courier;

        $label_update =  [
            'order_no' => $order_id,
            'awb_no' => strtoupper($tracking_id),
            'forwarder' => $courier
        ];
        Label::upsert($label_update, 'order_awb_no_unique', ['order_no', 'awb_no', 'forwarder']);
        return 'success';
    }

    public function editOrderAddress($order_id)
    {
        $order = config('database.connections.order.database');

        $order_details = DB::select("SELECT 
        seller_sku,order_item_identifier,title,
        GROUP_CONCAT(DISTINCT shipping_address) as shipping_address
        from $order.orderitemdetails 
        WHERE amazon_order_identifier = '$order_id'
        GROUP By order_item_identifier, seller_sku,title
        ");

        $qty_details = Label::query()->where('order_no', $order_id)
            ->get(['order_no', 'order_item_id', 'qty'])
            ->toArray();

        // po($order_details);
        $address = $order_details[0]->shipping_address;
        $sku = [];
        $title = [];
        foreach ($order_details as $value) {
            $sku[$value->order_item_identifier] = $value->seller_sku;
            $title[$value->order_item_identifier] = $value->title;
        }

        $shipping_address = $this->lableDataCleanup($address, 'address');
        $manage = json_decode($shipping_address, true);

        return Response(['address' => $manage, 'qty' => $qty_details, 'sku' => $sku, 'title' => $title]);
    }

    public function updateOrderAddress(Request $request, $id)
    {
        $validater = Validator::make($request->all(), [
            'name' => ['required'],
            'phone' => ['required'],
            'county' => ['required'],
            'countryCode' => ['required'],
            'city' => ['required'],
            'addressType' => ['required'],
            'addressLine1' => ['required'],
            'addressLine2' => ['required'],
        ]);

        if ($validater->fails()) {
            return response()->json([
                'status' => '400',
                'errors' => $validater->errors(),
            ]);
        } else {
            $json_data = [];
            $json_data = array(
                "Name" => htmlspecialchars($request->input('name')),
                "AddressLine1" => htmlspecialchars($request->input('addressLine1')),
                "AddressLine2" => htmlspecialchars($request->input('addressLine2')),
                "City" => htmlspecialchars($request->input('city')),
                "County" => htmlspecialchars($request->input('county')),
                "CountryCode" => htmlspecialchars($request->input('countryCode')),
                "Phone" => htmlspecialchars($request->input('phone')),
                "AddressType" => htmlspecialchars($request->input('addressType'))
            );
            $shipping_address = json_encode($json_data);

            $tracking_id = $request->input('tracking_id');
            $forwarder = $request->input('forwarder');

            $qty = $request->input('qty');

            foreach ($qty as $key => $value) {

                Label::where('order_item_id', (string)$key)
                    ->update(['qty' => $value]);
            }

            $order = config('database.connections.order.database');
            DB::select("UPDATE  $order.orderitemdetails 
                        SET shipping_address = '$shipping_address'
                         WHERE amazon_order_identifier = '$id'
                        ");

            $product_name = $request->input('title');
            foreach ($product_name as $key => $title) {

                $item_title = htmlspecialchars($title);
                DB::select("UPDATE  $order.orderitemdetails 
                        SET title = '$item_title'
                         WHERE order_item_identifier = '$key'
                        ");
            }

            Label::where('order_no', $id)
                ->update(
                    [
                        'awb_no' => $tracking_id,
                        'forwarder' => $forwarder
                    ]
                );

            return response()->json([
                'status' => '200',
                'message' => 'student updated successfully'
            ]);
        }
    }

    public function labelSearchByOrderId(Request $request)
    {
        if ($request->ajax()) {

            $data_type = $request->data_type;

            $search_value = [];
            if ($data_type == 'Outward Awb No' || $data_type == 'Inward Awb No') {
                $search_value = $this->labelSearchByAwbNo($request->value, $data_type);
            } else {

                $amazon_order_id = $request->value;
                $amazon_order_id_array = preg_split('/[\r\n| |:|,]/', $amazon_order_id, -1, PREG_SPLIT_NO_EMPTY);

                $amazon_order_id_array = array_unique($amazon_order_id_array);
                $amazon_order_id_string = "'" . implode("', '", $amazon_order_id_array) . "'";

                $label_detials = $this->labelListing($amazon_order_id_string, $data_type);

                $temp_label = array_unique(array_column($label_detials, 'order_no'));
                $temp_label = array_intersect_key($label_detials, $temp_label);
                $search_value =  $this->labelSearchDataFormating($temp_label, $amazon_order_id_array);
            }

            return $search_value;
        }
        return view('label.search_by_amazon_order_id');
    }

    public function labelSearchByAwbNo($data, $data_type)
    {
        $awb_no = array_unique(preg_split('/[\r\n| |:|,|.]/', $data, -1, PREG_SPLIT_NO_EMPTY));
        $awb_tracking_no = "'" . implode("','", $awb_no) . "'";
        $label_details = $this->labelListing($awb_tracking_no, $data_type);
        // return $label_details;

        return $this->labelSearchDataFormating($label_details, []);
    }

    public function  labelSearchByDate(Request $request)
    {
        if ($request->ajax()) {
            $date = $request->selected_date;
            $data = explode(' - ', $date);

            if (count($data) >= 2) {
                $split = [
                    'start'    => trim($data[0]),
                    'end'   =>     trim($data[1])
                ];
            } else {
                return 'Date Selection Went Wrong';
            }
            $id = json_encode($split);

            $label_details = $this->labelListing($id, 'search_date');
            $currentPageNumber = $request->start / $request->length + 1;
            // po($currentPageNumber);
            // exit;
            return DataTables::of($label_details)
                ->addColumn('check_box', function ($label_detail) use ($currentPageNumber) {
                    return "<input class='check_options' type='checkbox' data-current-page='$currentPageNumber' value='$label_detail->id' name='options[]' id='checkid$label_detail->id'>";
                })
                ->addColumn('store_name', function ($label_detail) {
                    $store_name = $label_detail->store_name;
                    return $store_name;
                })
                ->addColumn('order_no', function ($label_detail) {
                    $order_no = $label_detail->order_no;
                    return $order_no;
                })
                ->addColumn('awb_no', function ($label_detail) {
                    $awb_no = $label_detail->awb_no;
                    return $awb_no;
                })
                ->addColumn('courier_name', function ($label_detail) {
                    $courier_name = $label_detail->forwarder;
                    return $courier_name;
                })
                ->addColumn('order_date', function ($label_detail) {
                    $order_date = Carbon::parse($label_detail->purchase_date)->format('Y-m-d');
                    return $order_date;
                })
                ->addColumn('customer_name', function ($label_detail) {
                    $customer_name = [];
                    $customer = json_decode($this->lableDataCleanup($label_detail->shipping_address, 'address'), true);
                    if (isset($customer['Name'])) {

                        $customer_name = $customer['Name'];
                    }
                    return $customer_name;
                })
                ->addColumn('action', function ($label_detail) {
                    $action = "<td>
                                    <div class='d-flex'>
                                        <a href='/label/pdf-template/orderid-$label_detail->id' class='edit btn btn-success btn-sm view'  target='_blank'>
                                            <i class='fas fa-eye'></i> View 
                                        </a>

                                        <div class='d-flex pl-2'>
                                            <a href='/label/download-direct/orderid-$label_detail->id' class='edit btn btn-info btn-sm'>
                                                <i class='fas fa-download'></i> Download 
                                            </a>
                                        </div>
                                        <div class='d-flex pl-2'>

                                         <a id='edit-address' data-toggle='modal' data-id='$label_detail->order_item_identifier' data-amazon_order_identifier='$label_detail->order_no ' href='javascript:void(0)'  class='edit btn btn-primary btn-sm'>
                                            <i class='fas fa-address-card'></i> Edit Address</a>

                                        </div>
                                    </div>
                                    </td>
                                </tr>";
                    return $action;
                })

                ->rawColumns(['check_box', 'store_name', 'order_no', 'awb_no', 'courier_name', 'order_date', 'customer_name', 'action'])
                ->make(true);
        }

        return view('label.search_by_date');
    }

    public function LabelFileManagementMonitor(Request $request)
    {
        $type = $request->module_type;
        $file_check = fileManagementMonitoring($type);
        // po($file_check);
        return response()->json($file_check);
    }

    public function dayBydayZipDownload()
    {
        $html = '';
        $html_final = '';
        $count = 0;
        $path = (Storage::path("label"));
        $files = scandir($path);
        foreach ($files as $key => $file) {
            if (str_contains($file, 'dayByday')) {
                // $file = str_replace('&', '-', $file);

                if ($key > 1) {
                    $file_path = Storage::path('label' . '/' . $file);
                    if (is_dir($file_path)) {
                        $file_paths = scandir($file_path);
                        foreach ($file_paths as $zip_path) {
                            if ($zip_path == 'zip') {
                                $zip_path_array = scandir($file_path . '/' . $zip_path);
                                $count = 0;
                                foreach ($zip_path_array as $zip_key => $zip_file) {
                                    if ($zip_key > 1) {
                                        $count++;
                                        if ($count == 1) {
                                            $html .= "<div>Date: " . str_replace('dayByday', '', str_replace('+', '-', $file));
                                        }
                                        $html .=
                                            "<a href='/label/zip/download/$file/zip/$zip_file'>
                                                 <li class='ml-4'>Label Part " . $zip_key - 1 . ' ' . date("M-d-Y H:i:s.", filemtime("$file_path/$zip_path/$zip_file")) . "</li>
                                            </a>";
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

    public function labelSearchDataFormating($label_detials, $amazon_order_id_array)
    {
        $html = '';
        $name = '';
        $missing_html = '';
        $found_order_id = [];

        if (count($label_detials) > 0) {

            foreach ($label_detials as $label_det) {

                $order_date = Carbon::parse($label_det->purchase_date)->format('Y-m-d');
                $id = $label_det->id;
                $address = $label_det->shipping_address;
                $courier_name = $label_det->forwarder;
                $awb_no = $label_det->awb_no;
                $order_id = $label_det->order_no;
                $address_array = json_decode($this->lableDataCleanup($address, 'address'), true);
                if (isset($address_array['Name'])) {
                    $name = $address_array['Name'];
                }

                $found_order_id[] = $order_id;

                $html .= "<tr>
                            <td><input class='check_options' type='checkbox' value='$label_det->id' name='options[]' id='checkid$label_det->id'></td>
                            <td> $label_det->store_name </td> 
                            <td> $label_det->order_no </td>";

                if ($awb_no && $courier_name) {

                    $html .=      "<td> $awb_no </td> 
                              <td> $courier_name </td> ";
                } else {
                    $awb_exist = $awb_no ? $awb_no : '';
                    $courier_name_exist = $courier_name ? $courier_name : '';
                    $html .= "<td><input type ='text' placeholder='$awb_no' id ='tracking$order_id' value='$awb_exist'></td>
                        <td><input type ='text' placeholder ='$courier_name' id='courier$order_id' value ='$courier_name_exist'></td>";
                }

                $html .= "<td> $order_date </td> 
                            
                            <td> $name</td>";
                if ($name && $courier_name && $awb_no) {
                    $html .= "<td>
                                <div class='d-flex'>
                                    <a href='/label/pdf-template/orderid-$id' class='edit btn btn-success btn-sm view'  target='_blank'>
                                        <i class='fas fa-eye'></i> View 
                                    </a>
                                
                                    <div class='d-flex pl-2'>
                                        <a href='/label/download-direct/orderid-$id' class='edit btn btn-info btn-sm'>
                                            <i class='fas fa-download'></i> Download 
                                        </a>
                                    </div>
                                    <div class='d-flex pl-2'>
                                        
                                     <a id='edit-address' data-toggle='modal' data-id='$label_det->order_item_identifier' data-amazon_order_identifier='$order_id ' href='javascript:void(0)'  class='edit btn btn-primary btn-sm'>
                                        <i class='fas fa-address-card'></i> Edit Address</a>
                                        
                                    </div>
                                </div>
                                </td>
                            </tr>";
                }
                if (!$courier_name || !$awb_no) {

                    $html .= "<td>
                                <div class='d-flex'>
                                    <a id='$order_id' class='update btn btn-success btn-sm'>
                                        <i class='fas fa-upload'></i> Update
                                    </a>
                                </div>
                            <td>";
                }
            }
        }

        $missing_order = array_diff($amazon_order_id_array, $found_order_id);

        $missing_html .= $this->trackingDetailsMissing($missing_order);
        return [
            'success' => $html,
            'missing' => $missing_html,
        ];
    }

    public function labelDataFormating($id)
    {
        $label = '';
        $order = config('database.connections.order.database');
        $catalog = config('database.connections.catalog.database');
        $web = config('database.connections.web.database');
        $prefix = config('database.connections.web.prefix');

        $where_condition = "web.id in ($id)";

        $label = DB::select("SELECT ordetail.amazon_order_identifier,
        GROUP_CONCAT(DISTINCT web.order_no)as order_no,
        GROUP_CONCAT(DISTINCT web.awb_no) as awb_no,
        GROUP_CONCAT(DISTINCT web.forwarder) as forwarder,
        GROUP_CONCAT(DISTINCT ord.purchase_date) as purchase_date,
        GROUP_CONCAT(DISTINCT store.store_name) as store_name,
        GROUP_CONCAT(DISTINCT ordetail.shipping_address, '-address-separator-') as shipping_address,
        GROUP_CONCAT(ordetail.title SEPARATOR '-label-title-') as title,
        GROUP_CONCAT(ordetail.seller_sku SEPARATOR '-label-sku-') as sku,
        GROUP_CONCAT(ordetail.quantity_ordered SEPARATOR '-label-qty-') as qty
        -- GROUP_CONCAT(web.qty SEPARATOR '-label-qty-') as qty
        from ${web}.${prefix}labels as web
        JOIN ${order}.orders as ord ON ord.amazon_order_identifier = web.order_no
        JOIN ${order}.orderitemdetails as ordetail ON ordetail.amazon_order_identifier = ord.amazon_order_identifier
        JOIN ${order}.order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id
        WHERE $where_condition
        GROUP BY ordetail.amazon_order_identifier
        ORDER BY shipping_address
        ");

        $label_data = [];
        $order_no = '';
        $product[] = [
            'title' => NULL,
            'sku' => NULL,
            'qty' => NULL
        ];

        $ignore = explode(
            ',',
            trim(getSystemSettingsValue(
                'ignore_label_title_keys',
                'gun, lighter, gold, spark, Fuel, Heat, Oxygen, alcohols, flamable, seed, sliver, stone, leather, jewellery, fungicide, fertilizer, Magnet'
            ))
        );

        if (!$label) {
            return NULL;
        }
        $label_details_array = [];
        $product = [];

        foreach ($label as $key => $label_value) {
            foreach ($label_value as $key1 => $label_details) {
                if ($key1 == 'shipping_address') {
                    $buyer_address = [];

                    $new_label_add = preg_split('/-address-separator-,?/', $label_details);
                    $new_add = count($new_label_add) > 2 ? $new_label_add[1] : $new_label_add[0];
                    $shipping_address = json_decode($this->lableDataCleanup($new_add, 'address'));

                    foreach ((array)$shipping_address as $add_key => $add_details) {

                        if ($add_key == 'CountryCode') {
                            $country_name = Mws_region::where('region_code', $add_details)->get('region')->first();
                            if (isset($country_name->region)) {
                                $buyer_address['country'] = $country_name->region;
                            }
                        }
                        $buyer_address[$add_key] =  $add_details;
                    }

                    $label_data[$key1] = $buyer_address;
                } elseif ($key1 == 'package_dimensions') {
                    $dimensions = [];
                    $shipping_address = json_decode($label_details);
                    foreach ((array)$shipping_address as $add_key => $add_details) {
                        $dimensions[$add_key] =  $add_details;
                    }
                    $label_data[$key1] = $dimensions;
                } elseif ($key1 == 'title') {

                    $title_array = explode('-label-title-', $label_details);
                    $title_array = array_unique($title_array);

                    $max_text = 100;
                    if (count($title_array) > 6) {
                        $max_text = 35;
                    } elseif (count($title_array) > 4) {
                        $max_text = 50;
                    }

                    foreach ($title_array as $key2 => $title) {
                        $ignore_title = str_ireplace($ignore, '', $title);
                        $product[$key2][$key1] = substr_replace($ignore_title, '..', $max_text);

                        $sku_array = explode('-label-sku-', $label_value->sku);
                        $sku_array = array_unique($sku_array);
                        $product[$key2]['sku'] = $sku_array[$key2] ?? '';

                        $qty_array = explode('-label-qty-', $label_value->qty);
                        $product[$key2]['qty'] = $qty_array[$key2];
                    }
                } else {

                    $label_data[$key1] = $label_details;
                }
            }
            $label_data['product'] = $product;
            $label_details_array[] = $label_data;
            $product = [];
            $label_data = [];
        }

        // po($label_details_array);
        // exit;
        return $label_details_array;
    }

    public function lableDataCleanup($data, $type)
    {
        if ($type == 'address') {
            return  str_replace(array("\n", "\r"), ' ', $data);
        } elseif ($type == 'awb') {
            return preg_replace("/[^a-zA-Z0-9]/", "", strtoupper($data));
        }
        return $data;
    }

    public function GetArabicToEnglisText($order_ids)
    {
        $googleTranslatedText = [];
        foreach ($order_ids as $key1 => $order_id) {
            $getTranslatedText = GoogleTranslate::select('name', 'addressline1', 'addressline2', 'city', 'county')
                ->where('amazon_order_identifier', $order_id['amazon_order_identifier'])
                ->get()
                ->toArray();
            $googleTranslatedText[] = isset($getTranslatedText[0]) ? $getTranslatedText[0] : [];
        }
        return $googleTranslatedText;
    }

    public function LabelIndex(Request $request)
    {
        if ($request->ajax()) {
            $date = $request->date;
            $data = explode(' - ', $date);

            $split = [];
            if (count($data) >= 2) {
                $split = [
                    'start'    => trim($data[0]),
                    'end'   =>     trim($data[1])
                ];
            } else {
                return 'Date Selection Went Wrong';
            }
            $date = json_encode($split);
            $result = $this->labelListing($date, "search_date");
            $records = $this->customLabelListing($result);

            return DataTables::of($records)
                ->editColumn('name', function ($record) {
                    $name = json_decode($record->shipping_address);
                    if (isset($name->Name)) {
                        return $name->Name;
                    }
                    return 'NA';
                })
                ->editColumn('purchase_date', function ($record) {
                    $date = Carbon::parse($record->purchase_date)->toDateString();
                    return $date;
                })
                ->addColumn('action', function ($record) {
                    $action = "<div class='d-flex'><a id='custom_print_modal' data-toggle='modal' data-order_no='$record->order_no'  href='javascript:void(0)' class='btn btn-success btn-sm'>
                        <i class='fas fa-edit'></i> Custom Print </a></div>";

                    return $action;
                })
                ->rawColumns(['name', 'purchase_date', 'action'])
                ->make(true);
        }
        return view("label.custom_label.index");
    }

    public function customLabelListing($data)
    {
        $label_listing = [];
        foreach ($data as $record) {
            if (count(explode(",", $record->seller_sku)) >= 2) {

                $label_listing[] = $record;
            }
        }
        return $label_listing;
    }

    public function FetchCustomLabelRecord(Request $request)
    {
        $order_no = $request->order_no;

        $order = config('database.connections.order.database');
        $web = config('database.connections.web.database');
        $prefix = config('database.connections.web.prefix');

        $data = DB::select("SELECT
                            DISTINCT
                            orderDetails.amazon_order_identifier as order_no, 
                            GROUP_CONCAT(orderDetails.title SEPARATOR '-label-title-') as title,
                            GROUP_CONCAT( orderDetails.seller_sku SEPARATOR '-label-sku-')as sku,
                            GROUP_CONCAT(orderDetails.quantity_ordered SEPARATOR '-label-qty-') as qty
                            FROM ${order}.orders as ord 
                            JOIN ${order}.orderitemdetails as orderDetails ON orderDetails.amazon_order_identifier = ord.amazon_order_identifier
                            JOIN ${order}.order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id
                            WHERE ord.amazon_order_identifier = '$order_no'
                            GROUP BY orderDetails.amazon_order_identifier
                        ");

        return response()->json($data);
    }

    public function LabelPrintTemplate($order_identifier, $sku)
    {
        $sku = "'" . implode("','", explode("-", $sku)) . "'";

        $order = config('database.connections.order.database');
        $web = config('database.connections.web.database');
        $prefix = config('database.connections.web.prefix');


        $label = DB::select("SELECT 
                            orderDetails.amazon_order_identifier,
                            GROUP_CONCAT(DISTINCT web.order_no)as order_no,
                            GROUP_CONCAT(DISTINCT web.awb_no) as awb_no,
                            GROUP_CONCAT(DISTINCT web.forwarder) as forwarder,
                            GROUP_CONCAT(DISTINCT ord.purchase_date) as purchase_date,
                            GROUP_CONCAT(DISTINCT store.store_name) as store_name,
                            GROUP_CONCAT(DISTINCT orderDetails.shipping_address, '-address-separator-') as shipping_address,
                            GROUP_CONCAT(orderDetails.title SEPARATOR '-label-title-') as title,
                            GROUP_CONCAT(orderDetails.seller_sku SEPARATOR '-label-sku-') as sku,
                            GROUP_CONCAT(orderDetails.quantity_ordered SEPARATOR '-label-qty-') as qty
                            FROM ${web}.${prefix}labels as web
                            JOIN ${order}.orders as ord ON ord.amazon_order_identifier = web.order_no
                            JOIN ${order}.orderitemdetails as orderDetails ON orderDetails.amazon_order_identifier = ord.amazon_order_identifier
                            JOIN ${order}.order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id
                            WHERE web.order_no = '$order_identifier'
                            AND orderDetails.seller_sku IN ($sku)
                            GROUP BY orderDetails.amazon_order_identifier
                            ORDER BY shipping_address
                        ");

        $label_data = [];
        $order_no = '';
        $product[] = [
            'title' => NULL,
            'sku' => NULL,
            'qty' => NULL
        ];

        $ignore = explode(
            ',',
            trim(getSystemSettingsValue(
                'ignore_label_title_keys',
                'gun, lighter, gold, spark, Fuel, Heat, Oxygen, alcohols, flamable, seed, sliver, stone, leather, jewellery, fungicide, fertilizer, Magnet'
            ))
        );

        if (!$label) {
            return NULL;
        }
        $label_details_array = [];
        $product = [];

        foreach ($label as $key => $label_value) {
            foreach ($label_value as $key1 => $label_details) {
                if ($key1 == 'shipping_address') {
                    $buyer_address = [];

                    $new_label_add = preg_split('/-address-separator-,?/', $label_details);
                    $new_add = count($new_label_add) > 2 ? $new_label_add[1] : $new_label_add[0];
                    $shipping_address = json_decode($this->lableDataCleanup($new_add, 'address'));

                    foreach ((array)$shipping_address as $add_key => $add_details) {

                        if ($add_key == 'CountryCode') {
                            $country_name = Mws_region::where('region_code', $add_details)->get('region')->first();
                            if (isset($country_name->region)) {
                                $buyer_address['country'] = $country_name->region;
                            }
                        }
                        $buyer_address[$add_key] =  $add_details;
                    }

                    $label_data[$key1] = $buyer_address;
                } elseif ($key1 == 'package_dimensions') {
                    $dimensions = [];
                    $shipping_address = json_decode($label_details);
                    foreach ((array)$shipping_address as $add_key => $add_details) {
                        $dimensions[$add_key] =  $add_details;
                    }
                    $label_data[$key1] = $dimensions;
                } elseif ($key1 == 'title') {

                    $title_array = explode('-label-title-', $label_details);
                    $title_array = array_unique($title_array);

                    $max_text = 100;
                    if (count($title_array) > 6) {
                        $max_text = 35;
                    } elseif (count($title_array) > 4) {
                        $max_text = 50;
                    }

                    foreach ($title_array as $key2 => $title) {
                        $ignore_title = str_ireplace($ignore, '', $title);
                        $product[$key2][$key1] = substr_replace($ignore_title, '..', $max_text);

                        $sku_array = explode('-label-sku-', $label_value->sku);
                        $sku_array = array_unique($sku_array);
                        $product[$key2]['sku'] = $sku_array[$key2] ?? '';

                        $qty_array = explode('-label-qty-', $label_value->qty);
                        $product[$key2]['qty'] = $qty_array[$key2];
                    }
                } else {

                    $label_data[$key1] = $label_details;
                }
            }
            $label_data['product'] = $product;
            $label_details_array[] = $label_data;
            $product = [];
            $label_data = [];
        }

        $getTranslatedText = $this->GetArabicToEnglisText($label_details_array);
        $generator = new BarcodeGeneratorPNG();

        $result = [];
        $bar_code = [];

        foreach ($label_details_array as $value) {

            $barcode_awb = 'AWB-MISSING';
            try {

                $result[] = (object)$value;
                if (($value['awb_no'])) {
                    $barcode_awb = $value['awb_no'];
                }

                $bar_code[] = base64_encode($generator->getBarcode($this->lableDataCleanup($barcode_awb, 'awb'), $generator::TYPE_CODE_39));
            } catch (Exception $e) {

                $getMessage = $e->getMessage();
                $getCode = $e->getCode();
                $getFile = $e->getFile();

                $slackMessage = "Message: $getMessage
                Code: $getCode
                File: $getFile
                Awb_No: $barcode_awb";

                slack_notification('app360', 'Label Bar Code Error', $slackMessage);
            }
        }

        return view('label.multipleLabel', compact('result', 'bar_code', 'getTranslatedText'));
    }
}
