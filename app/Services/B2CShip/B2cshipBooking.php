<?php

namespace App\Services\B2CShip;

use Carbon\Carbon;
use App\Jobs\B2C\B2CBooking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;


class B2cshipBooking
{
    public function b2cdata($amazon_order_id)
    {

        $ord_details = DB::connection('order')
            ->select("SELECT 
                oids.*, 
                ord.amazon_order_identifier as order_id, 
                ord.purchase_date as order_date,
                ord.payment_method_details as pay_method,
                ord.order_item as item,
                ord.buyer_info as mail 
            FROM orders AS ord
           INNER join orderitemdetails AS oids
            on  ord.amazon_order_identifier = oids.amazon_order_identifier
            where
             oids.amazon_order_identifier = '$amazon_order_id'
              
        ");
        $consignee_data = [];
        foreach ($ord_details as $key => $details) {
            $OrderID = $details->order_id;

            if (is_null($details->order_date)) {
                $purchase_date = 'NA';
            } else {
                $purchase_date = $details->order_date;
            }


            if (is_null($details->pay_method)) {
                $payment_method = 'NA';
            } else {
                $payment_method = $details->pay_method;
            }

            if (is_null($details->item)) {
                $pieces = 'NA';
            } else {
                $pieces = $details->item;
            }
            if (is_null($details->order_item_identifier)) {
                $invoice_no = 'NA';
            } else {
                $invoice_no = $details->order_item_identifier;
            }


            $buyer_data = $details->mail;

            $buyer_info = json_decode($details->mail);

            if ($buyer_data == '{}' || is_null($buyer_data)) {
                $email = 'NA';
            } else {
                $email = $buyer_info->BuyerEmail;
            }


            $asin = $details->asin;
            $item_name = $details->title;

            $consignee_details = json_decode($details->shipping_address);
            $consignee_tax = Json_decode($details->item_tax);
            if (is_null($consignee_tax)) {
                $consignee_tax_amt = 'NA';
            } else {
                $consignee_tax_amt = $consignee_tax->Amount;
            }


            if (is_null($consignee_tax)) {
                $consignee_tax_currency = 'NA';
            } else {
                $consignee_tax_currency = $consignee_tax->CurrencyCode;
            }

            $item_price = Json_decode($details->item_price);
            if (is_null($item_price)) {
                $item_price = 'NA';
            } else {
                $item_price = $item_price->Amount;
            }

            $consignee_name = $this->objKeyVerify($consignee_details, 'Name');

            $consignee_AddressLine1 = $this->objKeyVerify($consignee_details, 'AddressLine1');
            $consignee_AddressLine2 = $this->objKeyVerify($consignee_details, 'AddressLine2');
            $consignee_city = $this->objKeyVerify($consignee_details, 'City');
            $consignee_state = $this->objKeyVerify($consignee_details, 'StateOrRegion');
            $consignee_CountryCode = $this->objKeyVerify($consignee_details, 'CountryCode');
            $consignee_pincode = $this->objKeyVerify($consignee_details, 'PostalCode');
            $consignee_Phone = $this->objKeyVerify($consignee_details, 'Phone');
            $consignee_AddressType = $this->objKeyVerify($consignee_details, 'AddressType');

            $cat_data =   DB::connection('catalog')->select("SELECT * FROM catalognewins  where asin = $asin");


            $value = $cat_data[0];

            $height = $value->height;
            $unit = $value->unit;
            $length = $value->length;
            $weight = $value->weight;
            $width = $value->width;

            $data['OrderID'] =     $OrderID;
            $data['purchase_date'] =  $purchase_date;
            $data['payment method'] = $payment_method;
            $data['pieces'] = $pieces;
            $data['email'] = $email;
            $data['item_name'] = $item_name;
            $data['consignee_name'] = $consignee_name;
            $data['consignee_AddressLine1'] = $consignee_AddressLine1;
            $data['consignee_AddressLine2'] = $consignee_AddressLine2;
            $data['consignee_city'] = $consignee_city;
            $data['consignee_state'] = $consignee_state;
            $data['consignee_CountryCode'] = $consignee_CountryCode;
            $data['invoice_no'] = $invoice_no;
            $data['invoice_value'] = $item_price;
            $data['consignee_pincode'] = $consignee_pincode;
            $data['consignee_Phone'] = $consignee_Phone;
            $data['consignee_AddressType'] = $consignee_AddressType;
            $data['consignee_tax_amt'] =  $consignee_tax_amt;
            $data['consignee_tax_currency'] =  $consignee_tax_currency;
            $data['height'] = $height;
            $data['unit'] =   $unit;
            $data['length'] = $length;
            $data['weight'] =  $weight;
            $data['width'] = $width;

            $consignee_data[] = $data;
        }

        $this->requestxml($consignee_data);
    }
    public function requestxml($consignee_data)
    {
        define('CUSTOMS_PERCENTAGE', 65);
        $consignee_values = $consignee_data;

        foreach ($consignee_values as $data) {

            $orddate = Carbon::now()->format('d-M-Y');


            $xml = '<?xml version="1.0" encoding="UTF-8"?>
                    <ShipmentBookingRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="ShipmentBookingRequest.xsd">

                    <UserId>humlofatro@vusra.com</UserId>
                    <Password>G79rC7</Password>
                    <APIVersion>1.0</APIVersion>
                    <Client>C10000026</Client>
                    <AwbNo></AwbNo>
                    <RefNo></RefNo>

                    <BookingDate>' . $orddate . '</BookingDate>

                    <ConsignorName>NITROUS HAUL INC</ConsignorName>
                    <ConsignorContactPerson>NITROUS HAUL INC</ConsignorContactPerson>
                    <ConsignorAddressLine1>75 22, 37th Ave,</ConsignorAddressLine1>
                    <ConsignorAddressLine2>Jackson Heights,</ConsignorAddressLine2>
                    <ConsignorAddressLine3></ConsignorAddressLine3>
                    <ConsignorCountry>USA</ConsignorCountry>
                    <ConsignorState>NY</ConsignorState>
                    <ConsignorCity>NY</ConsignorCity>
                    <ConsignorPinCode>11372</ConsignorPinCode>
                    <ConsignorMobileNo>16318127010</ConsignorMobileNo>
                    <ConsignorEmailID>nitroushaulinc@gmail.com</ConsignorEmailID>
                    <ConsignorTaxID></ConsignorTaxID>

                    <ConsigneeName>' . $this->clean($data['consignee_name']) . '</ConsigneeName>
                    <ConsigneeContactPerson></ConsigneeContactPerson>
                    <ConsigneeAddressLine1> ' . (!empty($data['consignee_AddressLine1']) ? $this->clean($data['consignee_AddressLine1']) : ',') . '</ConsigneeAddressLine1>
                    <ConsigneeAddressLine2>' . (!empty($data['consignee_AddressLine2']) ? $this->clean($data['consignee_AddressLine2']) : ',') . '</ConsigneeAddressLine2>
                    <ConsigneeAddressLine3></ConsigneeAddressLine3>
                    <ConsigneeCountry>IN</ConsigneeCountry>
                    <ConsigneeState>karnataka</ConsigneeState>
                    <ConsigneeCity> ' . strtolower($data['consignee_city']) . ' </ConsigneeCity>
                    <ConsigneePinCode> ' . $data['consignee_pincode'] . '</ConsigneePinCode>
                    <ConsigneeMobile>' . (($data['consignee_Phone'] == "") ? '9897654565' : $this->cleanph($data['consignee_Phone'])) . ' </ConsigneeMobile>
                    <ConsigneeEmailID> ' . $data['email'] . ' </ConsigneeEmailID>
                    <ConsigneeTaxID></ConsigneeTaxID>
                    <PacketType>SPX</PacketType>
                    <PaymentType>CREDIT</PaymentType>
                    <PacketDescription>' . $this->clean($data['item_name']) . '.</PacketDescription>
                    <InvoiceNo>' .  $data['invoice_no'] . '</InvoiceNo>
                    <InvoiceValue>' . $this->calculateCustomValue($data['invoice_value']) . '</InvoiceValue>
                    <CurrencyCode>USD</CurrencyCode>
                    <InvoiceValueINR>' . $data['invoice_value']  . '</InvoiceValueINR>
                    <CurrencyCodeINR>INR</CurrencyCodeINR>
                    <Pieces>' . $data['pieces'] . '</Pieces>
                    <ActualWeight>2</ActualWeight>

                    <PCSWeightDetails>
                        <PCSWeightDetail>
                             <Weight>' . (($data['weight'] > 0) ? $data['weight'] : 1) . '</Weight>
                             <Length>' . (($data['length'] > 0) ? $data['length'] : 1) . '</Length>
                             <Breadth>' . (($data['width'] > 0) ? $data['width'] : 1) . '</Breadth>
                             <Height>' . (($data['height'] > 0) ? $data['height'] : 1) . '</Height>
                            <PCS>1</PCS>
                        </PCSWeightDetail>
                    </PCSWeightDetails>

                    <PCSDescriptionDetails>
                        <PCSDescriptionDetail>
                            <Description>' . $this->clean($data['item_name']) . '.</Description>
                            <HSNCode>1</HSNCode>
                            <Unit>' . $data['pieces'] . '</Unit>
                            <UnitValue>' . $this->calculateCustomValue($data['invoice_value']) . '</UnitValue>
                        </PCSDescriptionDetail>
                    </PCSDescriptionDetails>
                </ShipmentBookingRequest>';

   
            $this->getawb($xml);

        }

    }
    public function getawb($xmldata)
    {
     
            $url = "https://api.b2cship.us/B2CShipAPI.svc/ShipmentBooking";

            $headers = array(
                "Content-type: text/xml",
                "Content-length: " . strlen($xmldata),
                "Connection: close",
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmldata);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $data = curl_exec($ch);
            // return $data;
            Log::info($data);

            if (curl_errno($ch))
                print curl_error($ch);
            else
                curl_close($ch);
            return $data;
        // }
    }

    public function calculateCustomValue($Data)
    {
        return round(($Data * (CUSTOMS_PERCENTAGE / 100)), 2);
    }

    public function cleanPh($string)
    {
        $string = str_replace('&', 'and', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
    }
    public  function clean($string)
    {
        $string = str_replace('&', 'and', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
    }
    public function objKeyVerify($obj, $key)
    {
        if (isset($obj->$key)) {
            return $obj->$key;
        }
        return 'NA';
    }
}
