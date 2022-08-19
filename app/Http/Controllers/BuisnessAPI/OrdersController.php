<?php

namespace App\Http\Controllers\BuisnessAPI;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\AWS_Business_API\AWS_POC\Orders;

class OrdersController extends Controller
{
    public function index()
    {
        $ApiCall = new Orders();
        $data = $ApiCall->getOrders();
        $responce = ($data[0]);
        $parse = simplexml_load_string($responce);
        $xml =  json_decode(json_encode($parse), true);
        $details = ($data[1]);
        $responce_code = ($xml["Response"]["Status"]["@attributes"]["code"]);
        $responce_text = ($xml["Response"]["Status"]["@attributes"]["text"]);
        $receved_payload = ($xml["@attributes"]["payloadID"]);

        $order_details_array = ($data[1]);
        $order_details = ($order_details_array[0]);
        $sent_payload = ($order_details["payload"]);
        $order_date = ($order_details["order_date"]);
        $org_name =  ($order_details["organization_name"]);
        $name =  ($order_details["name"]);
        $email = ($order_details["e_mail"]);
        $countrycode = ($order_details["country_code"]);
        $country_name = ($order_details["country_name"]);
        $order_id = ($order_details["order_id"]);

        $deliver1 =   ($order_details["delivery_1"]);
        $deliver2 =   ($order_details["delivery_2"]);
        $deliver3 =   ($order_details["delivery_3"]);
        $street = ($order_details["street"]);
        $city = ($order_details["city"]);
        $state = ($order_details["state"]);
        $post_code = ($order_details["post_code"]);
        $area_code = ($order_details["area_code"]);
        $phone_no  = ($order_details["phone_no"]);
        $fax_name  = ($order_details["fax_name"]);

        $asin = ($order_details["asin"]);
        $item_description = ($order_details["item_description"]);
        $unit = ($order_details["unit"]);
        $class = ($order_details["class"]);
        $quantity = ($order_details["quantity"]);
        $ManufacturerName = ($order_details["ManufacturerName"]);
        $line = ($order_details["line"]);
        $ManufacturerPartID = ($order_details["ManufacturerPartID"]);
        $category = ($order_details["category"]);
        $sub_category = ($order_details["sub_category"]);

        $item_details = [
            $asin,
            $item_description,
            $unit,
            $class,
            $quantity,
            $ManufacturerName,
            $line,
            $ManufacturerPartID,
            $category,
            $sub_category,

        ];

        $ship_address_array = [
            $deliver1,
            $deliver2,
            $deliver3,
            $street,
            $city,
            $state,
            $post_code,
            $area_code,
            $phone_no,
            $fax_name,
        ];

        // DB::connection('order')->table('business_orders')->insert([
        //     'sent_payload' => $sent_payload,
        //     'organization_name' => $org_name,
        //     'order_date' => $order_date,
        //     'name' => $name,
        //     'e-mail' => $email,
        //     'country_name' => $country_name,
        //     'country_code' => $countrycode,
        //     'order_id' => $order_id,
        //     'item_details' => json_encode($item_details),
        //     'ship_address' => json_encode($ship_address_array),
        //     'bill_address' => json_encode($ship_address_array),
        //     'responce_payload' => $receved_payload,
        //     'responce_text' =>  $responce_text,
        //     'responce_code' => $responce_code,
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]);

        return view('buisnessapi.orders.index', compact('data'));
    }
}
