<?php

namespace App\Console\Commands\BusinessAPI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\AWS_Business_API\AWS_POC\Orders;

class cliqnshop_orders_call extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:order_cliqnshop_place';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetch the data from cliqnshop order table and places order Through order API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = DB::connection('cliqnshop')->table('order')

            ->join('mshop_order_base_product as oid', function ($query) {
                $query->on('oid.baseid', '=', 'mshop_order.baseid')
                ->where('status','0');
            })
            ->join('mshop_product as pid', function ($query) {
                $query->on('pid.id', '=', 'oid.prodid');
            })
            ->select('code', 'label')
            ->get();

        $call = new Orders;
        foreach ($data as $val) {
            // po($data);
            $asin = ($val->code);
            $item_name  = ($val->label);


            $data =  $call->getOrders($asin, $item_name);
            // $resultxml = $data[2];
            // Storage::disk('local')->put('xml.txt', $resultxml);



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
                'xml_sent' => '',
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

            // DB::connection('business')->table('orders')->upsert($insert, ['order_id'], [
            //     'xml_sent',
            //     'sent_payload',
            //     'organization_name',
            //     'order_date',
            //     'name',
            //     'e-mail',
            //     'country_name',
            //     'country_code',
            //     'item_details',
            //     'ship_address',
            //     'bill_address',
            //     'responce_payload',
            //     'responce_text',
            //     'responce_code'
            // ]);

            $data = DB::connection('cliqnshop')->table('mshop_order_base_product')->where('prodcode', $asin)->update([
                'sent_xml' => $xml,
                'status' =>'1',
            ]);
     
        }
    }
}
