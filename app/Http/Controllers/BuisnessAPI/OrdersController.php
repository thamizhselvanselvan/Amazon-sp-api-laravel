<?php

namespace App\Http\Controllers\BuisnessAPI;

use Nette\Utils\Json;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Services\AWS_Business_API\AWS_POC\Orders;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;

class OrdersController extends Controller
{
    public function index()
    {
        // $asin = 'B017V4IMVQ';
        // $name = 'Harry Potter and the Sorcerers Stone, Book 1';
        // $ApiCall = new Orders();
        // $data = $ApiCall->getOrders($asin, $name);
        // dd('ok');
        // $resultxml = $data[2];
        // Storage::disk('local')->put('xml.txt', $resultxml);



        // $responce = ($data[0]);

        // $parse = simplexml_load_string($responce);
        // $xmlr =  json_decode(json_encode($parse), true);
        // $details = ($data[1]);
        // $responce_code = ($xmlr["Response"]["Status"]["@attributes"]["code"]);
        // $responce_text = ($xmlr["Response"]["Status"]["@attributes"]["text"]);
        // $receved_payload = ($xmlr["@attributes"]["payloadID"]);

        // $xml = ($data[2]);

        // $order_details_array = ($data[1]);
        // $order_details = ($order_details_array[0]);
        // $sent_payload = ($order_details["payload"]);
        // $order_date = ($order_details["order_date"]);
        // $org_name =  ($order_details["organization_name"]);
        // $name =  ($order_details["name"]);
        // $email = ($order_details["e_mail"]);
        // $countrycode = ($order_details["country_code"]);
        // $country_name = ($order_details["country_name"]);
        // $order_id = ($order_details["order_id"]);

        // $deliver1 =   ($order_details["delivery_1"]);
        // $deliver2 =   ($order_details["delivery_2"]);
        // $deliver3 =   ($order_details["delivery_3"]);
        // $street = ($order_details["street"]);
        // $city = ($order_details["city"]);
        // $state = ($order_details["state"]);
        // $post_code = ($order_details["post_code"]);
        // $area_code = ($order_details["area_code"]);
        // $phone_no  = ($order_details["phone_no"]);
        // $fax_name  = ($order_details["fax_name"]);

        // $asin = ($order_details["asin"]);
        // $item_description = ($order_details["item_description"]);
        // $unit = ($order_details["unit"]);
        // $class = ($order_details["class"]);
        // $quantity = ($order_details["quantity"]);
        // $ManufacturerName = ($order_details["ManufacturerName"]);
        // $line = ($order_details["line"]);
        // $ManufacturerPartID = ($order_details["ManufacturerPartID"]);
        // $category = ($order_details["category"]);
        // $sub_category = ($order_details["sub_category"]);

        // $item_details = [
        //     $asin,
        //     $item_description,
        //     $unit,
        //     $class,
        //     $quantity,
        //     $ManufacturerName,
        //     $line,
        //     $ManufacturerPartID,
        //     $category,
        //     $sub_category,

        // ];

        // $ship_address_array = [
        //     $deliver1,
        //     $deliver2,
        //     $deliver3,
        //     $street,
        //     $city,
        //     $state,
        //     $post_code,
        //     $area_code,
        //     $phone_no,
        //     $fax_name,
        // ];

        // DB::connection('business')->table('orders')->insert([
        //     // 'xml_sent' => json_encode($xml),
        //     'xml_sent' => '',
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

        // dd($responce, $details);

        // return view('buisnessapi.orders.index', compact('data'));
    }
    public function test()
    {

        $data[] = DB::connection('cliqnshop')->table('order')

            ->join('order_base_product as oid', function ($query) {
                $query->on('oid.baseid', '=', 'order.baseid')
                    ->where('status', '1');
            })
            ->join('product as pid', function ($query) {
                $query->on('pid.id', '=', 'oid.prodid');
            })
            ->get();


        $data_pending = DB::connection('cliqnshop')->table('order_base_product')
            ->select('prodcode')
            ->where('status', '0')
            ->get();
        $order_unplaced = count($data_pending);

        $data_orders = DB::connection('cliqnshop')->table('order_base_product')
            ->select('prodcode', 'name', 'quantity', 'price', 'status')
            ->get();


        return view('buisnessapi.orders.details', compact('data',  'order_unplaced'));
    }

    public function getorders(Request $request)
    {

        if ($request->ajax()) {
            $data_placed = DB::connection('cliqnshop')->table('order_base_product')
                ->select('prodcode', 'name', 'quantity', 'price', 'status')
                ->where('status', '1')
                ->get();
            // $order_placed = count($data_placed);

            return response()->json(['success' => ' successfull', 'data' => $data_placed]);
        }
    }

    public function orderspending(Request $request)
    {
        if ($request->ajax()) {
            $data_pending = DB::connection('cliqnshop')->table('order_base_product')
                ->select('prodcode', 'name', 'quantity', 'price', 'status')
                ->where('status', '0')
                ->get();
            return response()->json(['success' => ' successfull', 'data' => $data_pending]);
        }
    }

    public function prodoffers(Request $request)
    {
        $asin = $request->asin;
        $ApiCall = new ProductsRequest();
        $data = $ApiCall->getASINpr($asin);
        $offers_data = $data->includedDataTypes->OFFERS;
        $rasin = $data->asin;
        $ritem_name = $data->title;
        $responce_html = '';

        foreach ($offers_data as $data) {
            $price_amount = $data->price->value->amount;
            $responce_html .=
                "<div class='card-body '>
                    <div class='row '>
                        <div class='col'>
                             <input type='radio' class='offer-id' name='oid' value='$data->offerId'/>
                             <input type='hidden' class='asin' name='asin' value='$rasin'/>
                             <input type='hidden' class='item_name' name='item_name' value='$ritem_name'/>
                              Price: $price_amount<br>
                              Availability: $data->availability<br>
                              Info: $data->deliveryInformation.<br>
                         </div>
                    </div>
                </div>";
        }

        return $responce_html;
    }

    public function orderbooking(Request $request)
    {
        if ($request->ajax()) {

            $asin = $request->asin;
            $name = $request->item_name;
            $OfferID = $request->offerid;

            $ApiCall = new Orders();
            $data = $ApiCall->getOrders($asin, $name, $OfferID);

            $responce = ($data[0]);

            $parse = simplexml_load_string($responce);
            $xmlr =  json_decode(json_encode($parse), true);

            $details = ($data[1]);
            $responce_code = ($xmlr["Response"]["Status"]["@attributes"]["code"]);
            $responce_text = ($xmlr["Response"]["Status"]["@attributes"]["text"]);
            $receved_payload = ($xmlr["@attributes"]["payloadID"]);

            $xml = ($data[2]);

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
            $xmlasin = $asin;

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

            $insert = [
                'xml_sent' => $xml,
                'sent_payload' => $sent_payload,
                'organization_name' => $org_name,
                'order_date' => $order_date,
                'name' => $name,
                'e-mail' => $email,
                'country_name' => $country_name,
                'country_code' => $countrycode,
                'order_id' => $order_id,
                'item_details' => json_encode($item_details),
                'ship_address' => json_encode($ship_address_array),
                'bill_address' => json_encode($ship_address_array),
                'responce_payload' => $receved_payload,
                'responce_text' =>  $responce_text,
                'responce_code' => $responce_code,
                'created_at' => now(),
                'updated_at' => now()
            ];

            DB::connection('business')->table('orders')->upsert($insert, ['order_id'], [
                'xml_sent',
                'sent_payload',
                'organization_name',
                'order_date',
                'name',
                'e-mail',
                'country_name',
                'country_code',
                'item_details',
                'ship_address',
                'bill_address',
                'responce_payload',
                'responce_text',
                'responce_code'
            ]);

            $data = DB::connection('cliqnshop')->table('order_base_product')->where('prodcode', $asin)->update([
                'sent_xml' => $xml,
                'status' => '1',
            ]);
            return $data;
        }
    }

    public function confirmation(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::connection('cliqnshop')->table('order_confirmation')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
        return view('cliqnshop.confirm');
    }
    public function notification(Request $request)
    {
        if ($request->ajax()) {

            $ship = DB::connection('cliqnshop')->table('ship_notification')->get();
            return DataTables::of($ship)
                ->addIndexColumn()
                ->make(true);
        }
        return view('cliqnshop.notification');
    }

    public function booked(Request $request)
    {
        if ($request->ajax()) {

            $data_placed = DB::connection('cliqnshop')->table('order_base_product')
                ->select('prodcode', 'name', 'quantity', 'price', 'status')
                ->where('status', '1')
                ->orderby('baseid', 'desc')
                ->get();

            return DataTables::of($data_placed)
                ->addIndexColumn()
                ->make(true);
        }
        return view('cliqnshop.booked');
    }
}
