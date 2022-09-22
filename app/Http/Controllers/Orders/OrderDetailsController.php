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
        // dd($data);

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

        // return $request
        $order_id = $request->amazon_order_identifier;

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

        // $aa = [];
        $details = (json_decode($data[0]->shipping_address));
        $details->Name = $request->name;
        $details->AddressLine1 = $request->address_1;
        $details->AddressLine2 = $request->address_2;
        $details->City = $request->city;
        $details->StateOrRegion = $request->county;
        $details->CountryCode = $request->country;
        $details->Phone = $request->phone;
        $ship_address = json_encode($details);

        $buy_email = (json_decode($data[0]->email));
        $buy_email->BuyerEmail = $request->BuyerEmail;
        $buyer_email = json_encode($buy_email);

        $currecy_details = json_decode(($data[0]->item_price));
        $currecy_details->CurrencyCode = $request->CurrencyCode;
        $currecy_details->Amount = $request->Amount;
        $currency_details = json_encode($currecy_details);

        $title = $request->title;
        $sku = $request->sku;


        $item_identifier = $request->order_item_identifier;
        $asin = $request->asin;
        $mark_identifier = $request->marketplace_identifier;
        $purchase_date = $request->purchase_date;
        $order_status = $request->order_status;
        $full_chanel =  $request->fulfillment_channel;
        $sale_chanel =  $request->sales_channel;
        $shi_lev_service =  $request->ship_service_level;
        $amount = $request->Amount;
        $c_code = $request->CurrencyCode;
        $qty_ship = $request->quantity_shipped;
        $unship =  $request->unship;
        $ship_serve_level_cat =  $request->shipment_service_level_category;
        $early_ship_date = $request->earky_ship;
        $latest_ship_date = $request->latest_ship;
        $early_deli_date = $request->early_deli;
        $late_deli_date = $request->latest_deli;
        $last_update =  $request->last_update_date;
        $order_type = $request->order_type;
        $tax = $request->tax_amount;

        // dd($buy_email);

        DB::select("UPDATE 
                     ${order}.orders as ord 
                    JOIN ${order}.orderitemdetails as orditem
                    ON orditem.amazon_order_identifier = ord.amazon_order_identifier
                    set orditem.shipping_address = '$ship_address',
                        orditem.buyer_info = '$buyer_email',
                        orditem.title = '$title',
                        orditem.seller_sku = '$sku',
                        orditem.asin = '$asin',
                        orditem.order_item_identifier = '$item_identifier',
                        orditem.quantity_shipped = '$qty_ship',
                        orditem.item_price = '$currency_details',

                    ord.marketplace_identifier='$mark_identifier',
                    ord.purchase_date='$purchase_date',
                    ord.order_status= '$order_status',
                    ord.fulfillment_channel='$full_chanel',
                    ord.sales_channel= '$sale_chanel',
                    ord.ship_service_level='$shi_lev_service',
                    ord.shipment_service_level_category= '$ship_serve_level_cat',
                    ord.earliest_ship_date=${early_ship_date},
                    ord.latest_ship_date=${latest_ship_date},
                    ord.earliest_delivery_date=${early_deli_date},
                    ord.latest_delivery_date=${late_deli_date},
                    ord.last_update_date=${last_update},
                    ord.order_type='$order_type'
                    WHERE orditem.amazon_order_identifier='$order_id'
               ");

        return redirect()->intended('/orders/details/list')->with('success', 'Order  has been updated successfully');
    }
}
