<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\shipntrack\Operation\Label;

class ShipnTrackLabelManagementController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $records = Label::query()->orderBy('id', 'DESC')->get();

            return DataTables::of($records)
                ->addColumn('action', function ($records) {
                    $action = "<div class='d-flex'>
                                    <a href='/shipntrack/label/template/$records->id 'class='edit btn btn-success btn-sm ml-2 mr-2' target='_blank'>
                                        <i class='fas fa-eye'></i> View 
                                    </a>
                                    <a href='/shipntrack/label/template/download/$records->id 'class='edit btn btn-info btn-sm mr-2'>
                                    <i class='fas fa-download'></i> Download </a>
                                </div>";
                    return $action;
                })
                ->rawColumns(['action'])
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

        Label::upsert($info, ['order_item_bag_unique'], [
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

    public function LabelTemplate(Request $request)
    {
        $selectColumn = [
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
        ];
        $records = Label::query()
            ->select($selectColumn)
            ->where('id', $request->id)
            ->get()
            ->toArray();

        $generator = new BarcodeGeneratorPNG();
        $bar_code = base64_encode($generator->getBarcode($records[0]['awb_no'], $generator::TYPE_CODE_39));

        return view('shipntrack.Operation.Label.labelTemplate', compact('records', 'bar_code'));
    }
}
