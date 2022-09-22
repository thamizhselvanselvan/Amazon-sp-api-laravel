<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class OrderDetailsController extends Controller
{
    public function index()
    {
        return view('orders.orderdetails_list.index');
    }
    public function search(Request $request)
    {
        $order_id  = $request->orderid;
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
         JOIN ${order}.ord_order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id

        WHERE ord.amazon_order_identifier = '$order_id'
    ");

        if (empty($data[0])) {
            return redirect()->intended('/orders/details/list')->with('error', 'Order Not present. Or Invalid OrderID');
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
        return view('orders.orderdetails_list.index', compact('details', 'email_used', 'data', 'price_data', 'item_tax'));
    }
    public function update(Request $request)
    {
    
    }
}
