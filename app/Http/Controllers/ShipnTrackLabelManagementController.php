<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\ShipNTrack\Operation\ShipNTrackLabel;

class ShipnTrackLabelManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('LabelPdfTemplate');
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $records = DB::connection('shipntracking')->select("SELECT
                GROUP_CONCAT(DISTINCT id)as id, order_no,
                GROUP_CONCAT(DISTINCT order_item_id)as order_item_id,
                GROUP_CONCAT(DISTINCT order_date)as order_date,
                GROUP_CONCAT(DISTINCT customer_name)as customer_name,
                GROUP_CONCAT(DISTINCT bag_no)as bag_no,
                GROUP_CONCAT(DISTINCT awb_no)as awb_no,
                GROUP_CONCAT(DISTINCT forwarder)as forwarder,
                GROUP_CONCAT(DISTINCT order_date)as purchase_date,
                GROUP_CONCAT(DISTINCT sku)as seller_sku
                FROM labels        
                GROUP BY order_no
                ");

            return DataTables::of($records)
                ->addColumn('select_all', function ($records) {
                    return "<input class='check_options' type='checkbox' value='$records->id' name='options[]' >";
                })
                ->addColumn('action', function ($records) {
                    $id = $records->id;
                    $action = "<div class='d-flex'>
                                    <a href='/shipntrack/label/template/$id 'class='edit btn btn-success btn-sm ml-2 mr-2' target='_blank'>
                                        <i class='fas fa-eye'></i> View 
                                    </a>
                                    <a href='/shipntrack/label/pdf/download/$id 'class='edit btn btn-info btn-sm mr-2'>
                                    <i class='fas fa-download'></i> Download PDF </a>

                                    <a id='edit-address' data-toggle='modal' data-id='$records->id' href='javascript:void(0)' class='edit btn btn-secondary btn-sm'>
                                    <i class='fas fa-address-card'></i> Address </a>
                                </div>";
                    return $action;
                })
                ->rawColumns(['select_all', 'action'])
                ->make(true);
        }
        return view('shipntrack.Operation.Label.index');
    }

    public function FormSubmit(Request $request)
    {
        $info = $request->validate([
            'order_no' => 'required',
            'order_item_id' => 'required',
            'bag_no' => 'required',
            'forwarder' => 'required',
            'awb_no' => 'required',
            'order_date' => 'required',
            'customer_name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'county' => 'required',
            'country' => 'required',
            'phone' => 'required',
            'product_name' => 'required',
            'sku' => 'required',
            'quantity' => 'required',
        ]);

        ShipNTrackLabel::upsert($info, ['order_item_bag_unique'], [
            'order_no',
            'order_item_id',
            'bag_no',
            'forwarder',
            'awb_no',
            'order_date',
            'customer_name',
            'address',
            'city',
            'county',
            'country',
            'phone',
            'product_name',
            'sku',
            'quantity'
        ]);

        return redirect('shipntrack/label')->with('success', 'Record has been insert successfully!');
    }

    public function LabelDetails($id)
    {
        $details = $this->ShipntrackLabelDataFormatting($id);
        return response()->json($details);
    }

    public function LabelEdit(Request $request)
    {
        $ids = explode(',', $request->id);
        foreach ($ids as $key => $id) {
            $updated_data = [
                'customer_name' => $request->name,
                'phone' => $request->phone,
                'city' => $request->city,
                'county' => $request->county,
                'country' => $request->country,
                'address' => $request->addressLine1,
                'forwarder' => $request->forwarder,
                'awb_no' => $request->tracking_id,
                'quantity' => $request->qty[$key]

            ];

            ShipNTrackLabel::where('id', $id)->update($updated_data);
        }

        return redirect('shipntrack/label')->with('success', 'Record has been updated successfully!');
    }

    public function LabelPdfTemplate(Request $request)
    {
        $bar_code = [];

        $ids = implode(',', explode('-', $request->id));
        $records = $this->ShipntrackLabelDataFormatting($ids);
        foreach ($records as $key => $record) {

            $generator = new BarcodeGeneratorPNG();
            $bar_code[] = base64_encode($generator->getBarcode($record['awb_no'], $generator::TYPE_CODE_39));
        }

        return view('shipntrack.Operation.Label.LabelPdfTemplate', compact('records', 'bar_code'));
    }

    public function ShipntrackLabelDataFormatting($label_id)
    {
        $label_records = DB::connection('shipntracking')->select("SELECT 
                        GROUP_CONCAT(DISTINCT order_no)as order_no,
                        GROUP_CONCAT(DISTINCT order_item_id)as order_item_id,
                        GROUP_CONCAT(DISTINCT order_date)as order_date,
                        GROUP_CONCAT(DISTINCT customer_name)as customer_name,
                        GROUP_CONCAT(DISTINCT address)as address,
                        GROUP_CONCAT(DISTINCT city)as city,
                        GROUP_CONCAT(DISTINCT county)as county,
                        GROUP_CONCAT(DISTINCT country)as country,
                        GROUP_CONCAT(DISTINCT phone)as phone,
                        GROUP_CONCAT(DISTINCT awb_no) as awb_no,
                        GROUP_CONCAT(DISTINCT forwarder) as forwarder,
                        GROUP_CONCAT(product_name SEPARATOR '-label-item-') as product_name,
                        GROUP_CONCAT(sku SEPARATOR '-label-sku-') as sku,
                        GROUP_CONCAT(quantity SEPARATOR '-label-qty-') as quantity
                        from labels
                        WHERE id IN($label_id)
                        GROUP BY order_no
                        ");

        $records = [];
        foreach ($label_records as $key1 => $record) {

            foreach ($record as $key2 => $value) {

                if ($key2 == 'product_name') {

                    $item_name = explode('-label-item-', $value);
                    $records[$key1][$key2] = $item_name;
                } elseif ($key2 == 'sku') {

                    $sku = array_unique(explode('-label-sku-', $value));
                    $records[$key1][$key2] = $sku;
                } elseif ($key2 == 'quantity') {

                    $quantity = explode('-label-qty-', $value);
                    $records[$key1][$key2] = $quantity;
                }

                if ($key2 != 'product_name' && $key2 != 'sku' && $key2 != 'quantity') {

                    $records[$key1][$key2] = $value;
                }
            }
        }
        return $records;
    }

    public function LabelPdfDownload($id)
    {
        $current_url = URL::current();
        $url = str_replace('pdf/download', 'template', $current_url);

        $label_record = ShipNTrackLabel::where('id', $id)->get('awb_no')->toArray();
        $awbNo = $label_record[0]['awb_no'];

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
}
