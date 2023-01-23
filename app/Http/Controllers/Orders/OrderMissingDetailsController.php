<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use App\Models\order\ZohoMissing;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class OrderMissingDetailsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data  = ZohoMissing::orderby('id', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('asin', function ($row) {
                    $asin = $row['asin'];
                    return $asin . '  ' . "<a href='javascript:void(0)' value ='$asin'   class='badge badge-success' id='asin'><i class='fa fa-copy'></i></a>";
                })

                ->editColumn('amazon_order_id', function ($row) {
                    $amazon_order_id = $row['amazon_order_id'];
                    return $amazon_order_id . '  ' . "<a href='javascript:void(0)' value ='$amazon_order_id'   class='badge badge-success' id='order_id'><i class='fa fa-copy'></i></a>";
                })


                ->editColumn('order_item_id', function ($row) {
                    $order_item_id = $row['order_item_id'];
                    return $order_item_id . '  ' . "<a href='javascript:void(0)' value ='$order_item_id'   class='badge badge-success' id='order_item'><i class='fa fa-copy'></i></a>";
                })


                ->editColumn('price', function ($row) {
                    $price = $row['price'];
                    if ($price == 0) {
                        return '<a href="#" data-toggle="tooltip" title="No Price Found Update Price"><i class="fa fa-times wrong" color-"red" aria-hidden="true" ></i> </a>';
                    } else {
                        return $price;
                    }
                })


                ->editColumn('status', function ($row) {
                    $status = $row['status'];
                    if ($status == 0) {
                        return
                            '<a href="#" data-toggle="tooltip" title="No Price Found Update Price"><i class="fa fa-times wrong" color-"red" aria-hidden="true" ></i> </a>';
                    } else if ($status == 1) {
                        return  '<a href="#" data-toggle="tooltip" title="Price Updated"><i class="fa fa-check click" color-"" aria-hidden="true" ></i> </a>';
                    } else {
                        return $status;
                    }
                })
                ->addColumn('action', function ($row) {
                    $id = $row['asin'] . '_' . $row['amazon_order_id'] . '_' . $row['order_item_id'];
                    return "<div class='d-flex'><a href='javascript:void(0)' id='price_update' value ='$id'  class='edit btn btn-info btn-sm'><i class='fa fa-toggle-up'></i> Update Price</a>";
                })
                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }

        return view('orders.zoho.zohoprocemissing');
    }

    public function updateview(Request $request)
    {
        $data = $request->order_details;
        $asin = $data['0'];
        $order_id = $data['1'];
        $item_id = $data['2'];
        $price = $data['3'];
        ZohoMissing::where(['amazon_order_id' => $order_id, 'order_item_id' => $item_id, 'asin' => $asin])
            ->update(['price' => $price, 'status' => '1']);
        return response()->json(['success' => 'Updated Sucessfully']);
    }
}
