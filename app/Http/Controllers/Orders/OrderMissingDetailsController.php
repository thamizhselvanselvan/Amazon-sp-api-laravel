<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use App\Services\Zoho\ZohoApi;
use App\Models\order\ZohoMissing;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\order\US_Price_Missing;
use App\Models\order\OrderUpdateDetail;
use Illuminate\Support\Facades\Artisan;
use Yajra\DataTables\Facades\DataTables;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\order\OrderSellerCredentials;

class OrderMissingDetailsController extends Controller
{
    use ConfigTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data  = ZohoMissing::orderby('id', 'desc')->where('status', 0);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('title', function ($row) {
                    return $row->title . '  ' . copy_to_clipboard($row->title, 'title');
                })
                ->editColumn('asin', function ($row) {
                    return $row->asin . '  ' . copy_to_clipboard($row->asin, 'asin');
                })
                ->editColumn('amazon_order_id', function ($row) {
                    return $row->amazon_order_id . '  ' . copy_to_clipboard($row->amazon_order_id, 'order_id');
                })
                ->editColumn('order_item_id', function ($row) {
                    return $row->order_item_id . '  ' . copy_to_clipboard($row->order_item_id, 'order_item');
                })
                ->editColumn('price', function ($row) {
                    
                    if ($row->price == 0) {
                        return '<a href="#" data-toggle="tooltip" title="No Price Found Update Price"><i class="fa fa-times wrong" color="red" aria-hidden="true" ></i> </a>';
                    }

                    return $row->price;
                })
                ->editColumn('status', function ($row) {
           
                    if ($row->status == 0) {
                        return '<a href="#" data-toggle="tooltip" title="No Price Found Update Price"><i class="fa fa-times wrong" color="red" aria-hidden="true" ></i> </a>';
                    } 
                    
                    if ($row->status == 1) {
                        return '<a href="#" data-toggle="tooltip" title="Price Updated"><i class="fa fa-check click" color="" aria-hidden="true" ></i> </a>';
                    }

                    return $row->status;
                })
                ->addColumn('action', function ($row) {

                    $attributes = "data-asin=" . $row['asin'];
                    $attributes .= " data-order-id=" . $row['amazon_order_id'];
                    $attributes .= " data-order-item-id=" . $row['order_item_id'];
                    $attributes .= " data-country-code" . $row['country_code'];

                    return "<div class='d-flex'>
                                <a href='javascript:void(0)' id='price_update' {$attributes}  class='edit btn btn-info btn-sm'>
                                    <i class='fa fa-toggle-up'></i> Update Price
                                </a>
                            </div>";
                })
                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }

        return view('orders.zoho.zohoprocemissing');
    }

    public function updateview(Request $request)
    {
        $asin = $request->asin;
        $order_id = $request->order_id;
        $item_id = $request->item_id;
        $country_code = $request->country_code;
        $price = $request->price;

        if ($order_id == null || $item_id == null || $country_code == null) {
            return response()->json(['data' =>  'error']);
        }

        //zoho api update
        $zoho = new ZohoApi(new_zoho: false);
        $type = 'price Update Through app360';
        $zoho_lead_search = $zoho->search($order_id, $item_id, $type);

        if (!isset($zoho_lead_search['data'][0]['id'])) {

            return response()->json(['data' => 'error']);
        }

        $lead_id = $zoho_lead_search['data'][0]['id'];
        $type = 'price Update Through app360';
        $zoho->updateLead($lead_id, ["Product_Cost" => $price], $type);

        //table zoho_pricing Update
        ZohoMissing::where([
            'amazon_order_id' => $order_id, 
            'order_item_id' => $item_id, 
            'asin' => $asin
        ])
        ->update([
            'price' => $price, 
            'status' => 1
        ]);

        return response()->json(['data' => 'success']);
    }

    public function zohopriceupdated(Request $request)
    {
        if ($request->ajax()) {
            $data  = ZohoMissing::orderby('id', 'desc')
                ->where('status', '1')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = $row['status'];
                    if ($status == 0) {
                        return
                            '<a href="#" data-toggle="tooltip" title="No Price Found Update Price"><i class="fa fa-times wrong" color-"red" aria-hidden="true" ></i> </a>';
                    } else if ($status == 1) {
                        return  '<a href="#" data-toggle="tooltip" title="Price Updated"><i class="fa fa-check click" color-"" aria-hidden="true" ></i> </a>';
                    } else {
                        return 'Unknown';
                    }
                })
                ->escapeColumns([])
                ->make(true);
        }
        return view('orders.zoho.zohoproceupdated');
    }

    public function zohoforcedumpview(Request $request)
    {
        $stores = OrderSellerCredentials::select('store_name', 'seller_id', 'country_code')
            ->where('dump_order', 1)
            ->distinct()
            ->get();

        // if ($request->ajax()) {
        //     $data =   ProcessManagement::where(['module' => 'Orders', 'command_name' => 'mosh:zoho_force_dump','status' =>'0'])
        //         ->select('status')
        //         ->orderby('updated_at', 'desc')
        //         ->limit(1)
        //         ->get();


        //     $messsage = ["success" => 'ok'];
        //     if (count($data) > 0) {
        //         $messsage = ["error" => $data];
        //     }
        //     return response()->json(['success' => true, "data" => $messsage]);
        // }
        return view('orders.zoho.zohoforcedump', compact('stores'));
    }


    public function zohoforcedump(Request $request)
    {
        $order_ids = preg_split('/[\r\n| |:|,]/', $request->order_ids, -1, PREG_SPLIT_NO_EMPTY);

        if (count($order_ids) > 12) {
            return redirect('/orders/missing/force/dump/view')->with(['warning' => 'order Ids Must be Less Than 12 (Zoho Dump)']);
        }
        $store_id = $request->country_code;
        $orderids = implode(',', $order_ids);

        commandExecFunc("mosh:zoho_force_dump {$orderids} {$store_id}");
        
        return redirect('/orders/missing/force/dump/view')->with('success', 'Order Is Updating...');
    }

    public function zohosync(Request $request)
    {
        $order_ids = preg_split('/[\r\n| |:|,]/', $request->order_ids, -1, PREG_SPLIT_NO_EMPTY);;

        if (count($order_ids) > 12) {
            return redirect('/orders/missing/force/dump/view')->with(['warning' => 'order Ids Must be Less Than 10 (Zoho Sync)']);
        }
        $store_id = $request->store_data;
        $orderids = implode(',', $order_ids);

        commandExecFunc("mosh:get_edd {$orderids} {$store_id}");

        return redirect('/orders/missing/force/dump/view')->with('success', 'Order Is Updating...');
    }

    public function uspricemissing(Request $request)
    {

        if ($request->ajax()) {

            $data = US_Price_Missing::orderby('id', 'desc')->where('status', '0');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('title', function ($row) {
                    return $row->title . '  ' . copy_to_clipboard($row->title, 'title');
                })
                ->editColumn('asin', function ($row) {
                    return $row->asin . '  ' . copy_to_clipboard($row->asin, 'asin');
                })
                ->editColumn('amazon_order_id', function ($row) {
                    return $row->amazon_order_id . '  ' . copy_to_clipboard($row->amazon_order_id, 'order_id');
                })
                ->editColumn('price', function ($row) {

                    if ($row->price == 0) {
                        return '<a href="#" data-toggle="tooltip" title="No Price Found Update Price"><i class="fa fa-times wrong" color-"red" aria-hidden="true" ></i> </a>';
                    } 

                    return $row->price;
                })
                ->editColumn('status', function ($row) {
                    $status = $row['status'];

                    if ($status == 0) {
                        return '<a href="#" data-toggle="tooltip" title="No Price Found Update Price"><i class="fa fa-times wrong" color-"red" aria-hidden="true" ></i> </a>';
                    } 
                    
                    if($status == 1) {
                        return  '<a href="#" data-toggle="tooltip" title="Price Updated"><i class="fa fa-check click" color-"" aria-hidden="true" ></i> </a>';
                    }
                        
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $attributes = "data-asin=" . $row['asin'];
                    $attributes .= " data-order-id=" . $row['amazon_order_id'];
                    $attributes .= " data-order-item-id=" . $row['order_item_id'];

                    return "<div class='d-flex'>
                            <a href='javascript:void(0)' id='price_update' {$attributes} class='edit btn btn-info btn-sm'>
                                <i class='fa fa-toggle-up'></i> Update US Price
                            </a></div>";
                })
                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }

        return view('orders.uspricemissing');
    }
    public function uspriceupdate(Request $request)
    {

        $asin = $request->asin;
        $price = $request->price;
        $item_id = $request->item_id;
        $order_id = $request->order_id;

        if ($order_id == null || $asin == null || $price == null) {
            return response()->json(['data' =>  'error']);
        };

        $table_name = table_model_create(country_code: 'us', model: 'Pricing', table_name: 'pricing_');
        $table_name->where('asin', $asin)->update(['us_price' => $price]);

        OrderUpdateDetail::where([
            ['amazon_order_id', $order_id],
            ['order_item_id', $item_id],
        ])->update(
            [
                'booking_status' => 0
            ]
        );

        US_Price_Missing::query()
            ->where([
                'asin' => $asin,
                'order_item_id' => $item_id, 
                'amazon_order_id' => $order_id,
            ])
            ->update([
                'status' => 1,
                'price' => $price 
            ]);

        return response()->json(['data' => 'success']);
    }
}
