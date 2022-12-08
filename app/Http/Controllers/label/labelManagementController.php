<?php

namespace App\Http\Controllers\label;

use DateTime;
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
use GuzzleHttp\Promise\Create;
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

use Picqer\Barcode\BarcodeGeneratorHTML;
use Illuminate\Support\Facades\Validator;
use App\Models\order\OrderSellerCredentials;
use App\Services\SP_API\API\Order\missingOrder;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

class labelManagementController extends Controller
{
    private $order_details;
    public function SearchLabel(Request $request)
    {
        $data = $this->labelListing(2);
        // dd($data);
        if ($request->ajax()) {
            $currentPageNumber = $request->start / $request->length + 1;

            $bag_no = $request->bag_no;
            $data = $this->labelListing($bag_no);

            return DataTables::of($data)
                ->addColumn('select_all', function ($data) use ($currentPageNumber) {
                    $name = json_decode($data->shipping_address);
                    if (isset($name->Name)) {
                        return "<input class='check_options' type='checkbox' value='$data->id' data-current-page='$currentPageNumber' name='options[]' id='checkid$data->id'>";
                    }
                })
                ->addColumn('name', function ($data) {
                    $name = json_decode($data->shipping_address);
                    if (isset($name->Name)) {
                        return $name->Name;
                    }
                    return 'NA';
                })
                ->addColumn('action', function ($data) use ($bag_no) {
                    $table = '';
                    $name = json_decode($data->shipping_address);
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

    public function GetLabel(Request $request)
    {
        if ($request->ajax()) {

            $bag_no = $request->bag_no;
            return $bag_no;
            $data = $this->labelListing($bag_no);
            return DataTables::of($data)
                ->addColumn('select_all', function ($data) {

                    return "<input class='check_options' type='checkbox' value='25231' name='options[]' id='checkid25231'>";
                })
                ->rawColumns(['select_all'])
                ->make(true);

            return response()->json($data);
        }
    }

    public function manage(Request $request)
    {
        return view('label.manage');
    }

    public function showTemplate($id)
    {
        $id_array = explode('-', $id);
        if (isset($id_array[0]) && isset($id_array[1])) {
            $bag_no = $id_array[0];
            $table_id = $id_array[1];
            $result = $this->labelDataFormating("'$table_id'");

            $result = $result[0];
            $awb_no = $result['awb_no'];
            $forwarder = $result['forwarder'];

            if ($awb_no == '' || $awb_no == NULL) {
                $awb_no = 'AWB-MISSING';
            }
            $result = (object)$result;

            $generator = new BarcodeGeneratorPNG();
            $bar_code = base64_encode($generator->getBarcode($awb_no, $generator::TYPE_CODE_39));
            return view('label.labelTemplate', compact('result', 'bar_code', 'awb_no', 'forwarder', 'bag_no'));
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
            // ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
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
        // log::alert($file_path);
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
        $generator = new BarcodeGeneratorPNG();

        $result = [];
        $bar_code = [];

        foreach ($results as $value) {

            $barcode_awb = 'AWB-MISSING';

            $result[] = (object)$value;
            if (($value['awb_no'])) {
                $barcode_awb = $value['awb_no'];
            }

            $bar_code[] = base64_encode($generator->getBarcode($barcode_awb, $generator::TYPE_CODE_39));
        }

        return view('label.multipleLabel', compact('result', 'bar_code'));
    }

    public function DownloadSelected(Request $request)
    {
        $passid = $request->id;
        $bag_no = $request->bag_no;
        $current_page_number = $request->current_page_number;

        // Log::alert($current_page_number);
        $currenturl =  URL::current();

        // if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
        //     $base_path = base_path();
        //     $command = "cd $base_path && php artisan pms:label-bulk-zip-download $passid $currenturl $bag_no $current_page_number > /dev/null &";
        //     exec($command);
        // } else {
        //     Artisan::call('pms:label-bulk-zip-download' . ' ' . $passid . ' ' . $currenturl . ' ' . $bag_no . ' ' . $current_page_number);
        // }

        $user_id = Auth::user()->id;
        $header = ["data" => "${passid}_${currenturl}_${bag_no}_${current_page_number}"];
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
    public function downloadExcelTemplate()
    {
        $filepath = public_path('template/Label-Template.xlsx');
        return Response()->download($filepath);
    }

    public function upload()
    {
        return  view('label.upload');
    }

    public function uploadExcel(Request $request)
    {
        foreach ($request->files as $key => $files) {

            foreach ($files as $keys => $file) {

                $fileName = $file->getClientOriginalName();
                $fileName = uniqid() . ($fileName);
            }
        }
        $data = Excel::toArray([], $file);
        $Label_excel_data = [];

        foreach ($data as $header) {

            foreach ($header as $key => $excel_data) {

                if ($key != 0) {
                    $Label_excel_data[] = [
                        'order_no' => $excel_data[0],
                        'awb_no'    => $excel_data[1],
                        'bag_no'    => $excel_data[2],
                        'forwarder' => $excel_data[3],
                    ];
                }
            }
        }
        Label::upsert($Label_excel_data, ['order_awb_no_unique'], ['order_no', 'awb_no', 'bag_no', 'forwarder']);
        return response()->json(["success" => "All file uploaded successfully"]);
    }

    public function missing()
    {
        $selected_store = OrderSellerCredentials::where('dump_order', '1')
            ->where('get_order_item', '1')
            ->get(['seller_id', 'store_name', 'country_code']);

        return view('label.missing', compact('selected_store'));
    }

    public function missingOrderId(Request $request)
    {
        $seller = explode(',', $request->seller_id);
        $order_id = $request->order_id;
        $seller_id = $seller[0];
        $country_code = $seller[1];

        $datas = preg_split('/[\r\n| |:|,]/', $order_id, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($datas as $amazon_order_id) {
            if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

                GetOrder::dispatch(
                    [
                        'country_code' => $country_code,
                        'seller_id' => $seller_id,
                        'amazon_order_id' => $amazon_order_id
                    ]
                )->onConnection('redis')->onQueue('default');
            } else {
                GetOrder::dispatch(
                    [
                        'country_code' => $country_code,
                        'seller_id' => $seller_id,
                        'amazon_order_id' => $amazon_order_id
                    ]
                );
            }
        }
        return redirect('/label/manage')->with("success", "Order Details Is Updating, Please Wait.");
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
        GROUP_CONCAT(DISTINCT ordetail.shipping_address, '-address-separator-') as shipping_address,
        GROUP_CONCAT(ordetail.title SEPARATOR '-label-title-') as title,
        GROUP_CONCAT(ordetail.seller_sku SEPARATOR '-label-sku-') as sku,
        GROUP_CONCAT(ordetail.quantity_ordered SEPARATOR '-label-qty-') as qty
        from ${web}.${prefix}labels as web
        JOIN ${order}.orders as ord ON ord.amazon_order_identifier = web.order_no
        JOIN ${order}.orderitemdetails as ordetail ON ordetail.amazon_order_identifier = ord.amazon_order_identifier
        WHERE $where_condition
        GROUP BY ordetail.amazon_order_identifier
        ");

        // po($label);
        // exit;

        $label_data = [];
        $order_no = '';
        $product[] = [
            'title' => NULL,
            'sku' => NULL,
            'qty' => NULL
        ];

        $ignore = [
            'gun',
            'lighter',
            'gold',
            'spark',
            'fuel',
            'heat',
            'oxygen',
            'alcohols',
            'famable',
        ];

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
                    $shipping_address = json_decode($new_add);

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


                    foreach ($title_array as $key2 => $title) {

                        $ignore_title = str_ireplace($ignore, '', $title);
                        $product[$key2][$key1] = substr_replace($ignore_title, '....', 100);

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

        return $label_details_array;
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
            JOIN ${order}.ord_order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id

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
                    ord_order_seller_credentials osc ON osc.seller_id = oids.seller_identifier
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
        $where_condition = "web.bag_no = '${id}' ";

        if ($search_type == 'order_id') {
            $where_condition = "web.order_no IN ($id)";
        }
        if ($search_type == 'awb_tracking_id') {
            $where_condition = "web.awb_no IN ($id)";
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
             web.id, 
             web.awb_no, 
             web.forwarder,
             web.order_no,
             ord.purchase_date,
             store.store_name, 
             orderDetails.seller_sku, 
             orderDetails.shipping_address,
             orderDetails.order_item_identifier
            from ${web}.${prefix}labels as web
            JOIN ${order}.orders as ord ON ord.amazon_order_identifier = web.order_no
            JOIN ${order}.orderitemdetails as orderDetails ON orderDetails.amazon_order_identifier = web.order_no
            JOIN ${order}.ord_order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id
            WHERE $where_condition
        ");
        return $data;
    }

    public function labelSearchByOrderId(Request $request)
    {
        if ($request->ajax()) {

            $amazon_order_id = $request->order_id;
            $amazon_order_id_array = preg_split('/[\r\n| |:|,]/', $amazon_order_id, -1, PREG_SPLIT_NO_EMPTY);

            $amazon_order_id_array = array_unique($amazon_order_id_array);
            $amazon_order_id_string = "'" . implode("', '", $amazon_order_id_array) . "'";

            $label_detials = $this->labelListing($amazon_order_id_string, 'order_id');

            $temp_label = array_unique(array_column($label_detials, 'order_no'));
            $temp_label = array_intersect_key($label_detials, $temp_label);

            $label_detials = $temp_label;
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
                    $address_array = json_decode(($address), true);
                    if (isset($address_array['Name'])) {
                        $name = $address_array['Name'];
                    }

                    $found_order_id[] = $order_id;

                    $html .= "<tr>
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
        return view('label.search_by_amazon_order_id');
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

    public function editOrderAddress($order_item_identifier)
    {

        $order = config('database.connections.order.database');
        $order_details = DB::select("SELECT shipping_address,order_item_identifier
        from ${order}.orderitemdetails 
        WHERE order_item_identifier = '$order_item_identifier'");

        $shipping_address = $order_details[0]->shipping_address;
        $manage = json_decode($shipping_address, true);

        return Response($manage);
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
            'addressLine2' => ['required']
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

            $order = config('database.connections.order.database');
            DB::select("UPDATE  ${order}.orderitemdetails 
                        SET shipping_address = '$shipping_address'
                         WHERE amazon_order_identifier = '$id'
                        ");


            return response()->json([
                'status' => '200',
                'message' => 'student updated successfully'
            ]);
        }
    }

    public function labelSearchByAwnNo(Request $request)
    {
        $awb_no = array_unique(preg_split('/[\r\n| |:|,|.]/', $request->awb_no, -1, PREG_SPLIT_NO_EMPTY));
        $awb_tracking_no = "'" . implode("','", $awb_no) . "'";
        $label_detials = $this->labelListing($awb_tracking_no, 'awb_tracking_id');

        return response()->json($label_detials);
    }

    public function editordersearchbyid($order_item_identifier)
    {

        $order = config('database.connections.order.database');
        $order_details = DB::select("SELECT shipping_address,order_item_identifier
        from ${order}.orderitemdetails 
        WHERE order_item_identifier = '$order_item_identifier'");

        $shipping_address = $order_details[0]->shipping_address;
        $data = json_decode($shipping_address, true);


        return Response($data);
    }

    public function updateordersearchbyid(Request $request, $id)
    {

        $validater = Validator::make($request->all(), [
            'name' => ['required'],
            'phone' => ['required'],
            'county' => ['required'],
            'countryCode' => ['required'],
            'city' => ['required'],
            'addressType' => ['required'],
            'addressLine1' => ['required'],
            'addressLine2' => ['required']
        ]);

        if ($validater->fails()) {
            return response()->json([

                'status' => '400',
                'errors' => $validater->errors(),

            ]);
        } else {

            $json_data = [];
            $json_data = array(
                "Name" => $request->input('name'),
                "AddressLine1" => $request->input('addressLine1'),
                "AddressLine2" => $request->input('addressLine2'),
                "City" => $request->input('city'),
                "County" => $request->input('county'),
                "CountryCode" => $request->input('countryCode'),
                "Phone" => $request->input('phone'),
                "AddressType" => $request->input('addressType')
            );
            $shipping_address = json_encode($json_data);

            $order = config('database.connections.order.database');
            DB::select("UPDATE  ${order}.orderitemdetails 
                        SET shipping_address = '$shipping_address'
                         WHERE amazon_order_identifier = '$id'
                        ");


            return response()->json([
                'status' => '200',
                'message' => 'student updated successfully'
            ]);
        }
    }

    public function LabelFileManagementMonitor(Request $request)
    {
        $type = $request->module_type;
        $file_check = fileManagementMonitoring($type);
        // po($file_check);
        return response()->json($file_check);
    }

    public function  labelSearchByDate(Request $request)
    {
        if ($request->ajax()) {
            $date = $request->date;
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

            $label_detials = $this->labelListing($id, 'search_date');

            if (count($label_detials) > 0) {
                $html = '';
                $name = '';
                $found_order_id = [];

                foreach ($label_detials as $label_det) {

                    $order_date = Carbon::parse($label_det->purchase_date)->format('Y-m-d');
                    $id = $label_det->id;
                    $address = $label_det->shipping_address;
                    $courier_name = $label_det->forwarder;
                    $awb_no = $label_det->awb_no;
                    $order_id = $label_det->order_no;
                    $address_array = json_decode(($address), true);
                    if (isset($address_array['Name'])) {
                        $name = $address_array['Name'];
                    }

                    $found_order_id[] = $order_id;

                    $html .= "<tr>
                                <td> $label_det->store_name </td> 
                                <td> $label_det->order_no </td>";

                    if ($awb_no && $courier_name) {

                        $html .=      "<td> $awb_no </td> 
                                  <td> $courier_name </td> ";
                    } else {
                        $awb_exist = $awb_no ? $awb_no : '';
                        $courier_name_exist = $courier_name ? $courier_name : '';
                        // $html .= "<td><input type ='text' placeholder='$awb_no' id ='tracking$order_id' value='$awb_exist'></td>
                        //     <td><input type ='text' placeholder ='$courier_name' id='courier$order_id' value ='$courier_name_exist'></td>";

                        $html .= "<td> </td>
                                   <td> </td>";
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
                    } else {
                        $html .= "<td>
                                    <div class='d-flex'>
                                       <h5>Update Details</h5>
                                    </div>
                                    </td>
                                </tr>";
                    }
                }
                return [
                    'success' => $html,

                ];
            }
        }

        return view('label.search_by_date');
    }
}
