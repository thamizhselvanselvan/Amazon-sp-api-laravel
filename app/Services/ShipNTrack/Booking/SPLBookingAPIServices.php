<?php

namespace App\Services\ShipNTrack\Booking;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use App\Models\ShipNTrack\CourierTracking\SPLTracking;

class SPLBookingAPIServices
{
    public function SPL($records)
    {
        // code to get the Authntication token of SPL API
        $filePath = storage_path('app/business/SPLToken.txt');
        $AuthToken = File::get($filePath);


        $URL =  'https://dev.gnteq.app/api/gnconnect/Shipments' ; 
        

        $rawData = 
        [
            "customerCode" => "CC960465",
            "branchCode" => "BR534730",
            "airwaybillNumber" => "",
            "shippingDateTime" => "2023-03-30T09:29:04",
            "dueDate" => "",
            "descriptionOfGoods" => "Clothes",
            "foreignHAWB" => "",
            "numberOfPieces" => "1",
            "cod" => 0,
            "totalCod" => 0,
            "customsDeclaredValue" => 215,
            "customsDeclaredValueCurrency" => "SAR",
            "codCurrnecy" => "",
            "productType" => "DLV",
            "dutyHandling" => "DDP",
            "supplierCode" => "SPL",
            "labelFormat" => "PDF",
            "labelSize" => "6X4",
            "officeId" => "string",
            "sortaionCenter" => "string",
            "consignee" => [
                                "consigneeContact" => [
                                                            "personName" => "Manar Test",
                                                            "companyName" => "GnTeq",
                                                            "phoneNumber1" => "98732444444",
                                                            "phoneNumber2" => "",
                                                            "cellPhone" => "98732444444",
                                                            "emailAddress" => "manar.shaban@gnteq.com",
                                                            "type" => "",
                                                            "civilId" => ""
                                                        ],
                                "consigneeAddress" => [
                                                        "countryCode" => "SAU",
                                                        "city" => "Riyadh",
                                                        "district" => "Al salam",
                                                        "line1" => "customer address",
                                                        "line2" => "",
                                                        "line3" => "",
                                                        "postCode" => "",
                                                        "longitude" => "",
                                                        "latitude" => "",
                                                        "locationCode1" => "",
                                                        "locationCode2" => "",
                                                        "locationCode3" => ""
                                                    ]
                            ],
            "shipper" => [
                            "shipperAddress" => [
                                                    "countryCode" => "GBR",
                                                    "city" => "London",
                                                    "line1" => "warehouse London Test",
                                                    "line2" => "",
                                                    "line3" => "",
                                                    "postCode" => "SL3 0NS",
                                                    "longitude" => "",
                                                    "latitude" => "",
                                                    "locationCode1" => "",
                                                    "locationCode2" => "",
                                                    "locationCode3" => ""
                                                ],
                            "shipperContact" => [
                                                    "personName" => "Mahzuz",
                                                    "companyName" => "Mahzuz",
                                                    "phoneNumber1" => "3321323121321",
                                                    "phoneNumber2" => "",
                                                    "cellPhone" => "3321323121321",
                                                    "emailAddress" => "manar@gnteq.com",
                                                    "type" => ""
                                                ]
                        ],
            "items" => [
                [
                    "quantity" => 1,
                    "weight" =>     [
                                        "unit" => 1,
                                        "value" => 1
                                    ],
                    "customsValue"=>    [
                                            "currencyCode"=> "SAR",
                                            "value"=> 10
                                        ],
                    "comments" => "",
                    "reference" => "",
                    "commodityCode" => "",
                    "goodsDescription" => "Dress",
                    "countryOfOrigin" => "GBR",
                    "packageType" => "",
                    "containsDangerousGoods" => true
                ]
            ],
            "shipmentWeight" => [
                                    "value" => 1,
                                    "weightUnit" => 1,
                                    "length" => 1,
                                    "width" => 1,
                                    "height" => 1,
                                    "dimensionUnit" => 1
                                ],
            "reference" =>      [
                                    "shipperReference1" => "",
                                    "shipperNote1" => ""
                                ],
            "includeLabel" => true,
            "includeOfficeDetails" => false
        ];

       

        $response = Http::withToken($AuthToken)
                            ->withHeaders([
                                'Content-Type' => 'application/json',
                                'Accept' => 'application/json',
                            ])
                            ->post($URL, $rawData);

       

        if ($response->successful()) 
        {
            $responseData = $response->json();
            po($responseData);
        }
        else if ($response->status() === 401)
        {
            Log::notice("SPL Tracking  API Returned  Unauthorized Response ! , Generate token and try again.  ");
            return 0 ;
        }
        else
        {
            Log::error($response);
            Log::notice("An error occurred. Please try again later.");
            return 0 ;
        }

    }
}