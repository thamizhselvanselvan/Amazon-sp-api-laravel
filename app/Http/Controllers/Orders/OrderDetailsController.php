<?php

namespace App\Http\Controllers\Orders;

use App\Models\order\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\order\OrderUpdateDetail;
use Yajra\DataTables\Facades\DataTables;
use App\Models\order\OrderSellerCredentials;

class OrderDetailsController extends Controller
{
    public function index()
    {
        return view('orders.orderdetails_list.index');
    }

    public function update(Request $request)
    {
        $details = [
            'Name' => $request->name,
            'AddressLine1' => $request->address_1,
            'AddressLine2' => $request->address_2,
            'City' => $request->city,
            'StateOrRegion' => $request->county,
            'CountryCode' => $request->country,
            'Phone' => $request->phone,
        ];
        $ship_address = json_encode($details);

        $buy_email = [
            'BuyerEmail' => $request->BuyerEmail,
        ];
        $buyer_email = json_encode($buy_email);

        $currecy_details = [
            'CurrencyCode' => $request->CurrencyCode,
            'Amount' => $request->Amount,
        ];
        $currency_details = json_encode($currecy_details);

        $tax = [
            'CurrencyCode' => $request->rrencyCode,
            'Amount' => $request->tax_amount,
        ];
        $tax_details = json_encode($tax);


        $order = config('database.connections.order.database');
        $order_id = $request->amazon_order_identifier;
        $order_identifier = $request->order_item_identifier;
        DB::select("UPDATE 
                  ${order}.orders as ord 
                  JOIN ${order}.orderitemdetails as orditem
                  ON orditem.amazon_order_identifier = ord.amazon_order_identifier
                  set orditem.shipping_address = '$ship_address',
                  orditem.title = '$request->title',
                  orditem.seller_sku = '$request->sku',
                  orditem.quantity_ordered = $request->qty,
                  orditem.quantity_shipped ='$request->quantity_shipped',
                  orditem.asin = '$request->asin',
                  orditem.order_item_identifier = '$request->order_item_identifier',
                  orditem.item_price = '$currency_details',
                  orditem.item_tax = '$tax_details',
        

                  ord.buyer_info = '$buyer_email',
                  ord.marketplace_identifier='$request->marketplace_identifier',
                  ord.purchase_date='$request->purchase_date',
                  ord.order_status= '$request->order_status',
                  ord.fulfillment_channel='$request->fulfillment_channel',
                  ord.sales_channel= '$request->sales_channel',
                  ord.ship_service_level='$request->ship_service_level',
                  ord.shipment_service_level_category= '$request->shipment_service_level_category',
                  ord.earliest_ship_date = '$request->earky_ship',
                  ord.latest_ship_date = '$request->latest_ship',
                  ord.earliest_delivery_date = '$request->early_deli',
                  ord.latest_delivery_date = '$request->latest_deli',
                  ord.last_update_date = '$request->last_update_date',
                  ord.order_type='$request->order_type'

        WHERE orditem.order_item_identifier = '$order_identifier'
        ");


        return redirect()->intended('/orders/details/list')->with('success', 'Order  has  updated successfully');
    }

    public function bulksearch(Request $request)
    {
        if ($request->ajax()) {

            $order_id = preg_split('/[\r\n| |:|,]/', $request->orderid, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($order_id as $orderid) {

                // $orderdata[$orderid] = [
                //     'orderid' => $orderid,
                // ];
                $order_data = $orderid;

                $order = config('database.connections.order.database');
                $order_details[] = DB::select("SELECT 
                                orderdetails.*, 
                            ord.purchase_date,
                            ord.last_update_date,
                            ord.order_status,
                            ord.fulfillment_channel,
                            ord.sales_channel,
                            ord.ship_service_level,
                            ord.order_total ,
                            ord.number_of_items_shipped as ordqty,
                            ord.number_of_items_unshipped as unship,
                            ord.payment_method pa_type,
                            ord.marketplace_identifier,
                            ord.shipment_service_level_category,
                            ord.order_type ,
                            ord.earliest_ship_date earky_ship,
                            ord.latest_ship_date latest_ship,
                            ord.earliest_delivery_date as early_deli,
                            ord.latest_delivery_date as latest_deli,
                            ord.buyer_info as email,
                            ord.automated_shipping_settings,
                            store.store_name,
                            store.seller_id
                            from ${order}.orders as ord
                            JOIN ${order}.orderitemdetails as orderdetails ON ord.amazon_order_identifier = orderdetails.amazon_order_identifier
                            JOIN ${order}.order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id

                            WHERE ord.amazon_order_identifier = '$order_data'");
            }
            return response()->json(['success' => 'Searched Sucessfully', 'data' => $order_details]);
        }
    }

    public function bulkedit(Request $request)
    {
        $order_id  = $request->id;
        $order = config('database.connections.order.database');
        $data = DB::select("SELECT 
        orderdetails.*, 
       ord.purchase_date,
       ord.last_update_date,
       ord.order_status,
       ord.fulfillment_channel,
       ord.sales_channel,
       ord.ship_service_level,
       ord.order_total ,
       ord.number_of_items_shipped as ordqty,
       ord.number_of_items_unshipped as unship,
       ord.payment_method pa_type,
       ord.marketplace_identifier,
       ord.shipment_service_level_category,
       ord.order_type ,
       ord.earliest_ship_date earky_ship,
       ord.latest_ship_date latest_ship,
       ord.earliest_delivery_date as early_deli,
       ord.latest_delivery_date as latest_deli,
       ord.buyer_info as email,
       ord.automated_shipping_settings,
       store.store_name,
       store.seller_id
        from ${order}.orders as ord
        JOIN ${order}.orderitemdetails as orderdetails ON ord.amazon_order_identifier = orderdetails.amazon_order_identifier
         JOIN ${order}.order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id

        WHERE orderdetails.order_item_identifier = '$order_id'");

        if (empty($data[0])) {
            return redirect()->intended('/orders/details/list')->with('error', 'Order Not present. Or Invalid OrderID  ');
        }
        $details = [];
        $price_data = [];
        $item_tax = [];
        $email_used = [];
        if (isset($data[0])) {
            $details = json_decode($data[0]->shipping_address);
            $price_data = json_decode($data[0]->item_price);
            $item_tax = json_decode($data[0]->item_tax);
            $email_used = json_decode($data[0]->email);
        }
        return view('orders.orderdetails_list.view', compact('details', 'email_used', 'data', 'price_data', 'item_tax'));
    }

    public function orderStatistics(Request $request)
    {
        $stores = OrderSellerCredentials::select('store_name', 'store.store_id')
            ->join("order_update_details as store", 'order_seller_credentials.seller_id', '=', 'store.store_id')
            ->distinct()
            ->get();

        $request_store_id = $request->store_id;
        $url = "/orders/statistics";

        if (isset($request_store_id)) {
            $url = "/orders/statistics/" . $request_store_id;
        }

        if ($request->ajax()) {

            $data = OrderUpdateDetail::query()
                ->with(['order_seller_cred' => function ($query) {
                    $query->select("id", "store_name", 'seller_id');
                }])
                ->when($request->store_id, function ($query, $role) use ($request) {
                    return $query->where('store_id', $role);
                })
                ->orderBy('created_at', 'DESC')
                ->limit(100);
            return DataTables::of($data)
                ->addColumn('store_name', function ($aws_credentials) {

                    if ($aws_credentials->order_seller_cred) {
                        return $aws_credentials->order_seller_cred->store_name;
                    }
                    return 'NA';
                })
                ->editColumn('updated_at', function ($row) {

                    return $row->updated_at->toDateTimeString();
                })
                ->editColumn('booking_status', function ($row) {
                    if ($row->booking_status == 0) {
                        // return 'Not processed';
                        return '<i class="fa fa-minus "  aria-hidden="true"></i>';
                    } else if ($row->booking_status == 1) {
                        // return 'Booked';
                        return '<i class="fa fa-check click" color-"blue" aria-hidden="true"></i>';
                    } else if ($row->booking_status == 5) {
                        // return 'Under processing';
                        $response = '<i class="fa fa-spinner under" aria-hidden="true"></i>';

                        $courier = ($row->courier_awb) ? 1 : 0;
                        $courier_name = ($row->courier_name == "B2CShip") ? 1 : 0;

                        return  $response . " <span id='order_retry' class='badge badge-danger cursor-pointer' data-id='". $row->id ."' data-couriername='".$courier_name."' data-awb='".$courier."'>Retry</span>";
                    }
                    
                    return $row->booking_status;
                })
                ->editColumn('zoho_status', function ($row) {

                    $zoho_id = $row['zoho_id'];
                    $zoho_status = $row['zoho_status'];
                    $response = "";

                    if ($zoho_status == '0') {
                        $response = "<a href='#' data-toggle='tooltip' title='Not Processed'><i class='fa fa-minus not '  aria-hidden='true'></i> </a>";
                    } else if ($zoho_status == '1') {
                        $response = "<a href='#' data-toggle='tooltip' title='$zoho_id'><i class='fa fa-check click'  aria-hidden='true' ></i> </a>" .
                            '  ' . "<a href='javascript:void(0)' value ='$zoho_id'   class='badge badge-success' id='zoho_clipboard'><i class='fa fa-copy'></i></a>";
                    } else if ($zoho_status == '5') {

                        $response =  "<a href='#' data-toggle='tooltip' title='Under Processing'><i class='fa fa-spinner under' aria-hidden='true'></i> </a>";
                        $courier = ($row->courier_awb) ? 1 : 0;
                        $courier_name = ($row->courier_name == "B2CShip") ? 1 : 0;

                        $response .= " <span id='order_retry' class='badge badge-danger cursor-pointer' data-id='". $row->id ."' data-couriername='".$courier_name."' data-awb='".$courier."'>Retry</span>";
                        
                    } else {
                        $response = $zoho_status;
                    }

                    return $response;
                })
                ->editColumn('order_feed_status', function ($row) {
                    $message = $row['order_feed_status'];
                    if ($row['order_feed_status'] == 'success') {
                        return  '<a href="#" data-toggle="tooltip" title="AWB successfully updated to Amazon"><i class="fa fa-check click" color-"blue" aria-hidden="true" ></i> </a>';
                    } else  if ($row['order_feed_status'] == '') {

                        return '<a href="#" data-toggle="tooltip" title="Not Processed"><i class="fa fa-minus not" aria-hidden="true"></i> </a>';
                    } else {
                        return "<a href='#' data-toggle='tooltip' title='$message'><i class='fa fa-times wrong' color-'blue' aria-hidden='true' ></i> </a>";
                    }
                })
                ->editColumn('courier_awb', function ($row) {
                    $awb =  $row['courier_awb'];
                    if ($awb == '') {
                        return '';
                    } else {
                        return "<a href='https://b2cship.us/tracking/?$awb' target='_blank'>$awb</a>";
                    }
                })
                ->addColumn('order_date', function ($row) {
                    $now = Carbon::now();
                    $yest = Carbon::now()->subdays(1);
                    $purchase_date = Order::where('amazon_order_identifier', $row->amazon_order_id)
                        ->get('purchase_date')
                        ->first();
                    if (isset($purchase_date->purchase_date)) {
                        $date =  date('Y-m-d H:i:s', strtotime($purchase_date->purchase_date));
                        if ($date > $yest && $date < $now) {
                            return $this->CarbonGetDateDiff($date . '.000');
                        } else {
                            // return ($date) . ' ' . 'IST';
                            return $this->daysdiffrence($date . '.000');
                        }
                    } else {
                        return 'NA';
                    }
                })
                ->editColumn('amazon_order_id', function ($row) {
                    $order_id = $row['amazon_order_id'];
                    if ($order_id == '') {
                        return '';
                    }
                    return $order_id . ' ' . "<a href='javascript:void(0)' value ='$order_id'   class='badge badge-success' id='clipboard'><i class='fa fa-copy'></i></a>";
                })

                ->rawColumns(['store_name', 'order_status', 'order_date'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('orders.statistics', compact('stores', 'request_store_id', 'url'));
    }

    public function order_retry(Request $request) {

        if ($request->ajax()) {

            $order_row_id = $request->id;
            $order_courier = [];

            if($request->couriername == 1 && $request->courier == 0) {
                $order_courier = ["booking_status" => 0];
            }

            OrderUpdateDetail::where("id", $order_row_id)
                ->update(["zoho_status" => 0, ...$order_courier]);

            return response()->json(["success" => "order retrying now!"]);
        }

        return response()->json(["error" => "retry with ajax request"]);
    }


    public function CarbonGetDateDiff($date)
    {
        $date_details_array = ['Year', 'Month', 'Day', 'Hour', 'Minute'];
        $date = substr($date, 0, strpos($date, "."));
        $created = new Carbon($date);
        $now = Carbon::now();
        $differnce = $created->diff($now);

        $final_date = '';
        $count = 0;
        foreach ((array)$differnce as $key => $value) {
            if ($value != 0 && $count < 5 && $count > 2) {
                $final_date .= $value > 1 ? $value . ' ' . $date_details_array[$count] . 's, ' : $value . ' ' . $date_details_array[$count] . ',  ';
            }
            $count++;
        }
        $time = rtrim($final_date, ' ,') . ' Before';
        $date =  $differnce->days > 0 ? $differnce->days . ' Days' : '';

        return $date . ' ' . $time;
    }
    public function daysdiffrence($date)
    {
        $date_details_array = ['Year', 'Month', 'Day',];
        $date = substr($date, 0, strpos($date, "."));
        $created = new Carbon($date);
        $now = Carbon::now();
        $differnce = $created->diff($now);

        $final_date = '';
        $count = 0;
        foreach ((array)$differnce as $key => $value) {
            if ($value != 0 && $count < 3 && $count > 2) {
                $final_date .= $value > 1 ? $value . ' ' . $date_details_array[$count] . 's, ' : $value . ' ' . $date_details_array[$count] . ', ';
            }
            $count++;
        }
        $time = rtrim($final_date, ' ,') . ' Before';
        // $date =  $differnce->days > 0 ? $differnce->days . ' Days' : '';
        if ($differnce->days > 0 && $differnce->days  < 2) {
            $date =   $differnce->days . ' Day' . '';
        } else {
            $date =   $differnce->days . ' Days' . '';
        }

        return $date . ' ' . $time;
    }
}
