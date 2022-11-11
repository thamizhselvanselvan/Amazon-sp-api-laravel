<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\order\OrderUpdateDetail;
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
                            JOIN ${order}.ord_order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id

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
         JOIN ${order}.ord_order_seller_credentials as store ON ord.our_seller_identifier = store.seller_id

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

        if ($request->ajax()) {

            $data = OrderUpdateDetail::query()
                ->where('store_id', $request->id)
                ->orderBy('created_at', 'DESC')
                ->limit(50)
                ->get();
            return response()->json(['success' => 'Searched Sucessfully', 'data' => $data]);
        }

        return view('orders.statistics', compact('stores'));
    }
}
