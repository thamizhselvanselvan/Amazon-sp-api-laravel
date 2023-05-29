<?php

namespace App\Http\Controllers\BuisnessAPI;

use Carbon\Carbon;
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
    public function orderpending(Request $request)
    {


        if ($request->ajax()) {

            if ($request->has("site_id")) {
                //  \Illuminate\Support\Facades\Log::alert('validation failed');

                $data = DB::connection('cliqnshop')->table('mshop_order')
                    ->whereIn('mshop_order.statuspayment', [5, 6])

                    ->join('mshop_order_base_product as oid', function ($query) {
                        $query->on('oid.baseid', '=', 'mshop_order.baseid')
                            ->where('oid.status', '0')
                            ->where('oid.price','not like', '%-%');
                    })
                    ->join('mshop_product as pid', function ($query) {
                        $query->on('pid.id', '=', 'oid.prodid');
                    })
                    ->where('mshop_order.siteid', $request->site_id)
                    ->select('oid.prodcode', 'oid.name', 'oid.quantity', 'oid.price', 'pid.asin', 'mshop_order.siteid','oid.baseid')
                    // ->orderBy('oid.mtime','desc')
                    ->get();
            } else {
                $data = DB::connection('cliqnshop')->table('mshop_order')
                    ->whereIn('mshop_order.statuspayment', [5, 6])

                    ->join('mshop_order_base_product as oid', function ($query) {
                        $query->on('oid.baseid', '=', 'mshop_order.baseid')
                            ->where('oid.status', '0')
                            ->where('oid.price','not like', '%-%');
                    })
                    ->join('mshop_product as pid', function ($query) {
                        $query->on('pid.id', '=', 'oid.prodid');
                    })
                    ->select('oid.prodcode', 'oid.name', 'oid.quantity', 'oid.price', 'pid.asin', 'mshop_order.siteid','oid.baseid')
                    // ->orderBy('oid.mtime','desc')
                    ->get();
            }


            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('rebate', function ($data) {
                    $s_id = $data->siteid;
                    $check_rebate = $data->baseid;
                    $base_count =  DB::connection('cliqnshop')->table('mshop_order_base_product')
                    ->where(['siteid' => $s_id, 'baseid' => $check_rebate])->where('price','not like', '%-%')->count();
                    $rebate_check = DB::connection('cliqnshop')->table('mshop_order_base')
                    ->where(['siteid' => $s_id, 'id' => $check_rebate])->value('rebate');
                    if($base_count == 1)
                    {
                    return $rebate_check;
                    }
                    else
                    {
                        return "<p class='bg-info'>$rebate_check</p>";
                    }
                })

                ->addColumn('action', function ($data) {
                    $id = $data->asin;
                    return  "<div class='d-flex'><a href='javascript:void(0)' id='offers1' value ='$id' class='edit btn btn-success btn-sm offers1'><i class='fas fa-check'></i> Book</a>";
                })
                ->addColumn('total_price', function ($data) {
                    $s_id = $data->siteid;
                    $check_total = $data->baseid;
                    $base_count =  DB::connection('cliqnshop')->table('mshop_order_base_product')
                    ->where(['siteid' => $s_id, 'baseid' => $check_total])->where('price','not like', '%-%')->count();
                    $total_price = DB::connection('cliqnshop')->table('mshop_order_base')
                    ->where(['siteid' => $s_id, 'id' => $check_total])->value('price');
                    if($base_count == 1)
                    {
                    return $total_price;
                    }
                    else
                    {
                        return "<p class='bg-info'>$total_price</p>";
                    }
                })
                ->editColumn('site', function ($data) {
                    $id = $data->siteid;
                    $data = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid', $id)->select('code')->get();
                    $s_code =  (strtoupper($data[0]->code));
                    if ($s_code == 'UAE') {
                        return '<center><p class="text-danger">UAE</p></center>';
                    } else  if ($s_code == 'IN') {
                        return '<center><p class="text-success">India</p></center>';
                    } else {
                        return $s_code;
                    }
                })
                ->rawColumns(['action', 'site', 'total_price','rebate'])
                ->make(true);
        }

        $data['sites'] = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('buisnessapi.orders.details', $data);
    }

    public function getorders(Request $request)
    {

        if ($request->ajax()) {
            $data_placed = DB::connection('cliqnshop')->table('mshop_order_base_product')
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
            $data_pending = DB::connection('cliqnshop')->table('mshop_order_base_product')
                ->select('prodcode', 'name', 'quantity', 'price', 'status')
                ->where('status', '0')
                ->orderBy('id', 'DESC')
                ->get();
            return response()->json(['success' => ' successfull', 'data' => $data_pending]);
        }
    }
    public function prodoffers(Request $request)
    {
        $asin = $request->asin;
        $quantity = $request->quantity;

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
                             <input type='hidden' class='quantity' name='quantity' value='$quantity'/>
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
            $quantity = $request->quantity;

            $ApiCall = new Orders();
            $data = $ApiCall->getOrders($asin, $name, $OfferID, $quantity);

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
                'asin' => $asin,
                'item_name'  =>  $item_description,
                'unit' => $unit,
                'class' => $class,
                'quantity' => $quantity,
                'Manufacturer' => $ManufacturerName,
                'line' => $line,
                'ManufID' =>    $ManufacturerPartID,
                'category' =>    $category,
                'sub_category' =>   $sub_category,

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

            $data =   DB::connection('cliqnshop')->table('mshop_order_base_product')
                ->where('mp.asin', $asin)
                ->join('mshop_product as mp', 'mshop_order_base_product.prodcode', '=', 'mp.code')
                ->update([
                    'mshop_order_base_product.sent_xml' => $xml,
                    'mshop_order_base_product.status' => '1',
                ]);
            return $data;
        }
    }

    public function confirmation(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::connection('cliqnshop')->table('mshop_order_confirmation')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('notice_date', function ($data) {
                    return Carbon::parse($data->notice_date)->format('M d Y');
                })
                ->editColumn('order_date', function ($data) {
                    return Carbon::parse($data->order_date)->format('M d Y');
                })
                ->rawColumns(['notice_date', 'order_date'])
                ->make(true);
        }
        return view('Cliqnshop.confirm');
    }

    public function notification(Request $request)
    {
        if ($request->ajax()) {
            $ship = DB::connection('cliqnshop')->table('mshop_ship_notification')->get();
            return DataTables::of($ship)
                ->addIndexColumn()
                ->editColumn('notice_date', function ($data) {
                    return Carbon::parse($data->notice_date)->format('d M Y');
                })
                ->editColumn('shipment_date', function ($data) {
                    return Carbon::parse($data->shipment_date)->format('d M Y');
                })
                ->editColumn('delivery_date', function ($data) {
                    return Carbon::parse($data->delivery_date)->format('d M Y');
                })
                ->rawColumns(['notice_date', 'shipment_date', 'delivery_date'])
                ->make(true);
        }
        return view('Cliqnshop.notification');
    }

    public function booked(Request $request)
    {
        if ($request->ajax()) {
            $data =
                DB::connection('business')->table('orders')->select('sent_payload', 'order_date', 'order_id', 'item_details', 'responce_payload', 'responce_code', 'created_at')
                ->orderby('created_at', 'DESC')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('asin', function ($data) {
                    return (json_decode($data->item_details)->asin);
                })
                ->addColumn('item_name', function ($data) {
                    return (json_decode($data->item_details)->item_name);
                })
                ->addColumn('quantity', function ($data) {
                    return (json_decode($data->item_details)->quantity);
                })
                ->editColumn('status', function ($data) {
                    return ($data->responce_code == '200') ? 'Order Request Sent' : 'Something Went Wrong Contact Admin';
                })

                ->rawColumns(['asin', 'item_name', 'quantity', 'status'])
                ->make(true);
        }
        return view('Cliqnshop.booked');
    }
}
