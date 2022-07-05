<?php

namespace App\Http\Controllers\label;

use DateTime;
use ZipArchive;
use RedBeanPHP\R;
use App\Models\Label;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;

use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

class labelManagementController extends Controller
{
    private $order_details;
    public function SearchLabel()
    {
        return view('label.search_label');
    }

    public function GetLabel(Request $request)
    {
        if ($request->ajax()) {

            $bag_no = $request->bag_no;
            $order = config('database.connections.order.database');
            $catalog = config('database.connections.catalog.database');
            $web = config('database.connections.web.database');

            $data = DB::select("SELECT
    
        DISTINCT web.id, web.awb_no, web.order_no, ord.purchase_date, store.store_name, orderDetails.seller_sku, orderDetails.shipping_address
        from $web.labels as web
        JOIN $order.orders as ord ON ord.amazon_order_identifier = web.order_no
        JOIN $order.orderitemdetails as orderDetails ON orderDetails.amazon_order_identifier = web.order_no
        JOIN $order.ord_order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id
        JOIN $catalog.catalog as cat ON cat.asin = orderDetails.asin
        WHERE web.bag_no = $bag_no
    ");
            return response()->json($data);
        }
    }

    public function manage(Request $request)
    {
        $data = $this->bladeOrderDetails();
        // dd($data);
        if ($request->ajax()) {

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($id) {

                    $this->order_details = $this->labelDataFormating($id->id);
                    if ($this->order_details) {
                        $action = '<div class="d-flex pl-5"><a href="/label/pdf-template/' . $id->id . ' " class="edit btn btn-success btn-sm" target="_blank"><i class="fas fa-eye"></i> View </a>';
                        $action .= '<div class="d-flex pl-2"><a href="/label/download-direct/' . $id->id . ' " class="edit btn btn-info btn-sm"><i class="fas fa-download"></i> Download </a>';
                        $action .= '<div class="text-center pl-3"><i class="fa fa-check-circle" style="color:green" aria-hidden="true"></i>';
                        return $action;
                    }
                    // $action1 = '<div class="pl-2"><input class="" type="checkbox" value='.$id['id'].' name="options[]" ></div>';
                    $action = "<div class ='text-center d-flex pl-5'>Details Not Avaliable
                    <div class='text-center  pl-5'><i class='fa fa-times' style='color:red' aria-hidden='true'></i>";
                    return $action;
                })
                ->addColumn('sn', function ($id) {
                    return $id->id;
                })
                ->addColumn('check_box', function ($id) {
                    if ($this->order_details) {
                        $check_box = '<div class="pl-2"><input class="check_options" type="checkbox" value=' . $id->id . ' name="options[]" ></div>';
                        return $check_box;
                    }
                })
                // ->editColumn('status', function () {
                //     if ($this->order_details) {
                //         return '<div class="text-center"><i class="fa fa-check-circle" style="color:green" aria-hidden="true"></i>';
                //     }
                //     return '<div class="text-center"><i class="fa fa-times" style="color:red" aria-hidden="true"></i>';
                // })
                ->editColumn('purchase_date', function ($date) {
                    $purchase_date = date('Y-m-d', strtotime($date->purchase_date));
                    return $purchase_date;
                })
                ->editColumn('customer_name', function ($customer_name) {
                    $customer_name = (array) json_decode($customer_name->shipping_address);
                    if (isset($customer_name['Name'])) {
                        return $customer_name['Name'];
                    }
                    return 'NA';
                })
                ->rawColumns(['sn', 'action', 'check_box', 'purchase_date', 'customer_name'])
                ->make(true);
        }
        return view('label.manage');
    }

    public function showTemplate($id)
    {
        //Single view
        $result = $this->labelDataFormating($id);
        $awb_no = $result['awb_no'];
        $result = (object)$result;

        // dd($result);
        $generator = new BarcodeGeneratorPNG();
        $bar_code = base64_encode($generator->getBarcode($awb_no, $generator::TYPE_CODE_93));
        return view('label.labelTemplate', compact('result', 'bar_code', 'awb_no'));
    }
    public function ExportLabel(Request $request)
    {
        //Single Download
        $url = $request->url;
        $awb_no = $request->awb_no;
        $file_path = 'label/label' . $awb_no . '.pdf';

        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }
        $exportToPdf = storage::path($file_path);
        Browsershot::url($url)
            //->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
            ->paperSize(576, 384, 'px')
            ->pages('1')
            ->scale(1.44)
            ->margins(0, 0, 0, 0)
            ->savePdf($exportToPdf);

        return response()->json(['Save pdf sucessfully']);
    }

    public function downloadLabel($awb_no)
    {
        return Storage::download('label/label' . $awb_no . '.pdf');
    }

    public function DownloadDirect($id)
    {

        $result = DB::connection('web')->select("select * from labels where id = '$id' ");
        $awb_no = $result[0]->awb_no;
        $file_path = 'label/label' . $awb_no . '.pdf';

        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }
        $exportToPdf = storage::path($file_path);
        $currentUrl = URL::current();
        $url = str_replace('download-direct', 'pdf-template', $currentUrl);

        Browsershot::url($url)
            ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
            ->pages('1')
            ->scale(1.44)
            ->margins(0, 0, 0, 0)
            ->savePdf($exportToPdf);

        return $this->downloadLabel($awb_no);
    }

    public function PrintSelected($id)
    {
        $allid = explode('-', $id);
        foreach ($allid as $id) {
            $results = $this->labelDataFormating($id);
            $result[] = (object)$results;
            $generator = new BarcodeGeneratorHTML();
            $bar_code[] = $generator->getBarcode($results['awb_no'], $generator::TYPE_CODE_93);
        }

        return view('label.multipleLabel', compact('result', 'bar_code'));
    }

    public function DownloadSelected(Request $request)
    {

        $passid = $request->id;
        $currenturl =  URL::current();

        $excelid = explode('-', $passid);

        foreach ($excelid as $getId) {
            // $id = Label::where('id', $getId)->get();
            $id = DB::connection('web')->select("select * from labels where id = '$getId' ");

            foreach ($id as $key => $value) {

                $awb_no = $value->awb_no;
                $url = str_replace('select-download', 'pdf-template', $currenturl . '/' . $getId);

                $path = 'label/label' . $awb_no . '.pdf';
                if (!Storage::exists($path)) {
                    Storage::put($path, '');
                }
                $exportToPdf = storage::path($path);
                Browsershot::url($url)
                    ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
                    ->paperSize(576, 384, 'px')
                    ->pages('1')
                    ->scale(1.44)
                    ->margins(0, 0, 0, 0)
                    ->savePdf($exportToPdf);

                $saveAsPdf[] = 'label' . $awb_no . '.pdf';
            }
        }

        return response()->json($saveAsPdf);
    }

    public function zipDownload($arr)
    {
        // po($arr);
        $replace = explode(',', $arr);
        $zip = new ZipArchive;
        $path = 'label/zip/' . 'label.zip';
        $fileName = Storage::path('label/zip/' . 'label.zip');
        Storage::delete($path);
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }
        if ($zip->open($fileName, ZipArchive::CREATE) === TRUE) {
            foreach ($replace as $key => $value) {
                $path = Storage::path('label/' . $value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
        return response()->download($fileName);
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
        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

        foreach ($request->files as $key => $files) {

            foreach ($files as $keys => $file) {

                $fileName = $file->getClientOriginalName();
                $fileName = uniqid() . ($fileName);
            }
        }
        $data = Excel::toArray([], $file);
        $header_value = [];
        $excel_data = [];

        foreach ($data as $header) {
            foreach ($header as $key => $header_data) {
                if ($key == 0) {
                    foreach ($header_data as $headerKey => $excel_header) {
                        if ($excel_header) {
                            $header_value[$headerKey] = $excel_header;
                        }
                    }
                } else {
                    foreach ($header_data as $valueKey => $excel_value) {
                        if ($excel_value) {
                            $excel_data[$key][$valueKey] = $excel_value;
                        }
                    }
                }
            }
        }

        foreach ($excel_data as $data) {

            $label = R::dispense('labels');
            $label->status = 0;
            foreach ($data as $key => $value) {
                if (isset($header_value[$key])) {

                    $column = lcfirst($header_value[$key]);
                    $label->$column = $value;
                }
            }
            $date = new DateTime(date('Y-m-d'));
            $created_at = $date->format('Y-m-d');
            // $label->order_date = $created_at;
            R::store($label);
        }

        return response()->json(["success" => "All file uploaded successfully"]);
    }

    public function labelDataFormating($id)
    {
        $order = config('database.connections.order.database');
        $catalog = config('database.connections.catalog.database');
        $web = config('database.connections.web.database');

        $label = DB::select("SELECT cat.asin,
        GROUP_CONCAT(DISTINCT web.order_no)as order_no,
        GROUP_CONCAT(DISTINCT web.awb_no) as awb_no,
        GROUP_CONCAT(DISTINCT ord.purchase_date) as purchase_date,
        GROUP_CONCAT(DISTINCT ordetail.shipping_address) as shipping_address,
        GROUP_CONCAT(DISTINCT ordetail.item_price) as order_total,
        -- GROUP_CONCAT(DISTINCT cat.item_dimensions) as item_dimensions,
        GROUP_CONCAT(DISTINCT cat.package_dimensions) as package_dimensions,
        GROUP_CONCAT(DISTINCT cat.title) as title,
        GROUP_CONCAT(DISTINCT ordetail.seller_sku) as sku,
        GROUP_CONCAT(DISTINCT ordetail.quantity_ordered) as qty
        from $web.labels as web
        JOIN $order.orders as ord ON ord.amazon_order_identifier = web.order_no
        JOIN $order.orderitemdetails as ordetail ON ordetail.amazon_order_identifier = ord.amazon_order_identifier
        JOIN $catalog.catalog as cat ON cat.asin = ordetail.asin
        WHERE web.id = $id
        GROUP BY cat.asin
    ");

        $label_data = [];
        $order_no = '';
        $product[] = [
            'title' => NULL,
            'sku' => NULL,
            'qty' => NULL
        ];

        if (!$label) {
            return NULL;
        }
        foreach ($label as $key => $label_value) {
            foreach ($label_value as $key1 => $label_detials) {

                if ($key1 == 'shipping_address') {
                    $buyer_address = [];
                    $shipping_address = json_decode($label_detials);
                    foreach ((array)$shipping_address as $add_key => $add_details) {
                        $buyer_address[$add_key] =  $add_details;
                    }
                    $label_data[$key1] = $buyer_address;
                } elseif ($key1 == 'package_dimensions') {
                    $dimensions = [];
                    $shipping_address = json_decode($label_detials);
                    foreach ((array)$shipping_address as $add_key => $add_details) {
                        $dimensions[$add_key] =  $add_details;
                    }
                    $label_data[$key1] = $dimensions;
                } elseif ($key1 == 'title') {

                    $product[$key][$key1] = $label_detials;
                } elseif ($key1 == 'sku') {

                    $product[$key][$key1] = $label_detials;
                } elseif ($key1 == 'qty') {

                    $product[$key][$key1] = $label_detials;
                } elseif ($key1 == 'asin') {
                } elseif ($key1 == 'order_total') {
                    $product[$key][$key1] = json_decode($label_detials);
                } else {

                    $label_data[$key1] = $label_detials;
                }
            }
        }
        $label_data['product'] = $product;
        // dd($label_data);
        return $label_data;
    }


    public function bladeOrderDetails()
    {
        $order = config('database.connections.order.database');
        $catalog = config('database.connections.catalog.database');
        $web = config('database.connections.web.database');

        $data = DB::select("SELECT

    DISTINCT web.id, web.awb_no, web.order_no, ord.purchase_date, store.store_name, orderDetails.seller_sku, orderDetails.shipping_address
    from $web.labels as web
    JOIN $order.orders as ord ON ord.amazon_order_identifier = web.order_no
    JOIN $order.orderitemdetails as orderDetails ON orderDetails.amazon_order_identifier = web.order_no
    JOIN $order.ord_order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id

    -- JOIN ord ON ord.our_seller_identifier = $order.ord_order_seller_credentials.seller_id as
");

        return $data;
    }
    // INNER JOIN $order.orders as ord ON ord.amazon_order_identifier = web.order_no
}
