<?php

namespace App\Http\Controllers\label;

use DateTime;
use ZipArchive;
use RedBeanPHP\R;
use App\Models\Label;
use App\Models\Mws_region;
use Illuminate\Http\Request;
use App\Jobs\Orders\GetOrder;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;

use Picqer\Barcode\BarcodeGeneratorHTML;
use App\Models\order\OrderSellerCredentials;
use App\Services\SP_API\API\Order\missingOrder;
use League\Csv\Reader;

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
            $prefix = config('database.connections.web.prefix');
            
            $data = DB::select("SELECT
            DISTINCT web.id, web.awb_no, web.order_no, ord.purchase_date, store.store_name, orderDetails.seller_sku, orderDetails.shipping_address
            from ${web}.${prefix}labels as web
            JOIN ${order}.orders as ord ON ord.amazon_order_identifier = web.order_no
            JOIN ${order}.orderitemdetails as orderDetails ON orderDetails.amazon_order_identifier = web.order_no
            JOIN ${order}.ord_order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id
            -- JOIN $catalog.catalog as cat ON cat.asin = orderDetails.asin
            WHERE web.bag_no = ${bag_no}
            ");
            return response()->json($data);
        }
    }

    public function manage(Request $request)
    {
        return view('label.manage');
    }

    public function showTemplate($id)
    {
        //Single view
        $result = $this->labelDataFormating($id);
        $awb_no = $result['awb_no'];
        $forwarder = $result['forwarder'];

        if ($awb_no == '' || $awb_no == NULL) {
            $awb_no = 'AWB-MISSING';
        }
        $result = (object)$result;

        // dd($result);
        $generator = new BarcodeGeneratorPNG();
        $bar_code = base64_encode($generator->getBarcode($awb_no, $generator::TYPE_CODE_39));
        return view('label.labelTemplate', compact('result', 'bar_code', 'awb_no', 'forwarder'));
    }
    public function ExportLabel(Request $request)
    {
        //Single Download
        $this->deleteAllPdf();
        $url = $request->url;
        $awb_no = $request->awb_no;
        $file_path = 'label/label' . $awb_no . '.pdf';

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

    public function downloadLabel($awb_no)
    {
        return Storage::download('label/label' . $awb_no . '.pdf');
    }

    public function DownloadDirect($id)
    {
        $this->deleteAllPdf();
        // $result = DB::connection('web')->select("select * from labels where id = '$id' ");
        $result = Label::where('id', $id)->get();
        $awb_no = $result[0]->awb_no;
        $file_path = 'label/label' . $awb_no . '.pdf';

        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }
        $exportToPdf = storage::path($file_path);
        $currentUrl = URL::current();
        $url = str_replace('download-direct', 'pdf-template', $currentUrl);

        Browsershot::url($url)
            ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
            ->paperSize(576, 384, 'px')
            ->pages('1')
            ->scale(1)
            ->margins(0, 0, 0, 0)
            ->savePdf($exportToPdf);

        return $this->downloadLabel($awb_no);
    }

    public function PrintSelected($id)
    {
        $allid = explode('-', $id);
        $generator = new BarcodeGeneratorPNG();
        foreach ($allid as $id) {
            $results = $this->labelDataFormating($id);

            $result[] = (object)$results;

            $barcode_awb = 'AWB-MISSING';

            if (($results['awb_no'])) {
                $barcode_awb = $results['awb_no'];
            }
            $bar_code[] = base64_encode($generator->getBarcode($barcode_awb, $generator::TYPE_CODE_39));
        }

        return view('label.multipleLabel', compact('result', 'bar_code'));
    }

    public function DownloadSelected(Request $request)
    {

        $passid = $request->id;
        $currenturl =  URL::current();

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:label-bulk-zip-download $passid $currenturl > /dev/null &";
            exec($command);
        } else {
            Artisan::call('pms:label-bulk-zip-download' . ' ' . $passid . ' ' . $currenturl);
        }

        return response()->json(['success' => 'Zip created successfully']);
    }

    public function zipDownload()
    {
        if (!Storage::exists('label/zip/label.zip')) {
            return redirect()->intended('/label/search-label')->with('success', 'File is not available right now! Please wait.');
        }
        return Storage::download('label/zip/label.zip');
    }

    // public function zipDownload($arr)
    // {
    //     // po($arr);
    //     $replace = explode(',', $arr);
    //     $zip = new ZipArchive;
    //     $path = 'label/zip/' . 'label.zip';
    //     $fileName = Storage::path('label/zip/' . 'label.zip');
    //     Storage::delete($path);
    //     if (!Storage::exists($path)) {
    //         Storage::put($path, '');
    //     }
    //     if ($zip->open($fileName, ZipArchive::CREATE) === TRUE) {
    //         foreach ($replace as $key => $value) {
    //             $path = Storage::path('label/' . $value);
    //             $relativeNameInZipFile = basename($path);
    //             $zip->addFile($path, $relativeNameInZipFile);
    //         }
    //         $zip->close();
    //     }
    //     return response()->download($fileName);
    // }

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

                if($key != 0){
                    $Label_excel_data [] = [
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
                )->onConnection('redis')->onQueue('order');
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

        $label = DB::select("SELECT ordetail.asin,
        GROUP_CONCAT(DISTINCT web.order_no)as order_no,
        GROUP_CONCAT(DISTINCT web.awb_no) as awb_no,
        GROUP_CONCAT(DISTINCT web.forwarder) as forwarder,
        GROUP_CONCAT(DISTINCT ord.purchase_date) as purchase_date,
        GROUP_CONCAT(DISTINCT ordetail.shipping_address) as shipping_address,
        GROUP_CONCAT(DISTINCT ordetail.item_price) as order_total,
        -- GROUP_CONCAT(DISTINCT cat.item_dimensions) as item_dimensions,
        -- GROUP_CONCAT(DISTINCT cat.package_dimensions) as package_dimensions,
        GROUP_CONCAT(DISTINCT ordetail.title) as title,
        GROUP_CONCAT(DISTINCT ordetail.seller_sku) as sku,
        GROUP_CONCAT(DISTINCT ordetail.quantity_ordered) as qty
        from ${web}.${prefix}labels as web
        JOIN ${order}.orders as ord ON ord.amazon_order_identifier = web.order_no
        JOIN ${order}.orderitemdetails as ordetail ON ordetail.amazon_order_identifier = ord.amazon_order_identifier
        -- JOIN $catalog.catalog as cat ON cat.asin = ordetail.asin
        WHERE web.id = ${id}
        GROUP BY ordetail.asin
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

        // $csv = Reader::createFromPath($file, 'r');

        // foreach ($csv as $key => $data) {

        //     if ($key > 0) {
        //         $Order = $data[0];
        //         $Name = $data[1];
        //         $AddressLine1 = $data[2];
        //         $AddressLine2 = $data[3];
        //         $City = $data[4];
        //         $County = $data[5];
        //         $CountryCode = $data[6];
        //         $Phone = $data[7];
        //         $AddressType = $data[8];

        //         $address_array = [
        //             'Name' => $Name,
        //             'AddressLine1' => $AddressLine1,
        //             'AddressLine2' => $AddressLine2,
        //             'City' => $City,
        //             'County' => $County,
        //             'CountryCode' => $CountryCode,
        //             'Phone' => $Phone,
        //             'AddressType' => $AddressType
        //         ];

        //         $address_json = (json_encode($address_array));
        //         DB::connection('order')->select("
        //             UPDATE orderitemdetails SET shipping_address  = '$address_json' 
        //             WHERE amazon_order_identifier = '$Order'
        //         ");
        //     }
        //     Log::info($Order);
        // }
        return response()->json(["success" => "All file uploaded successfully"]);
    }
}
