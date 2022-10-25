<?php

namespace App\Services\AWS_Business_API\AWS_POC;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class Orders
{
    public function getOrders($asin, $item_name, $OfferID)
    {
        $val = random_int(100, 10000);
        $random = substr(md5(mt_rand()), 0, 7);
        $uniq = $random . $val . '@moshecom.com';
        $date = Carbon::now()->format('d-m-Y');
        $countrycode = 'US';
        $country = 'USA';
        $country_name = 'united States Of America';
        $email = 'tech@moshecom.com';
        $name = 'mr.Robert';

        /* ship and bill details  */

        $final_item_name = str_replace(array('&', '<', '>', ';'), ' ', $item_name);
        $orderID = random_int(100, 10000);
        $org_name = 'nitrous';
        $deli1 = 'Tech Team, tech@moshecom.com, 325 9th Ave N,Seattle,Washington';
        $deli2 = '';
        $deli3 = '';
        $street = '325 9th Ave N';
        $city = 'Seattle';
        $state = 'Washington';
        $postcode = '98109';
        $area_code = '206';
        $ph_no = '9110674543';

        $addressID = substr(md5(mt_rand()), 0, 9);
        $fax_name = 'nitrous';
        $comments = 'nitrous,Tech Team, tech@moshecom.com,Bangalore ';
        $extrinsic = 'Nitrous';
        $supplierPartAuxiliaryID  = substr(md5(mt_rand()), 0, 9);
        $tax = '1';
        $currency = 'USD';
        /* item details  */
        $money  = 10;
        $asin = $asin;
        $offer = $OfferID;
        $item_description = $final_item_name;
        $unit = 'EA';
        $class = 'NA';
        $manuname = 'NA';
        $manu_id = 'NA';
        $subcatagory = 'NA';
        $catagory = 'NA';
        $line = '1';
        $qty = '1';

        $base[] = [
            'payload' => $uniq,
            'order_date' => $date,
            'country_code' => $countrycode,
            'country_name' => $country_name,
            'e_mail' => $email,
            'name' => $name,
            'price' => $money,
            'order_id' => $orderID,
            'organization_name' => $org_name,
            'delivery_1' => $deli1,
            'delivery_2' => $deli2,
            'delivery_3' => $deli3,
            'street' => $street,
            'city' => $city,
            'state' => $state,
            'post_code' => $postcode,
            'area_code' => $area_code,
            'phone_no' => $ph_no,
            'address_id' => $addressID,
            'fax_name' => $fax_name,
            'comments' => $comments,
            'exen' => $extrinsic,
            'aux_id' => $supplierPartAuxiliaryID,


            'asin' => $asin,
            'item_description' => $item_description,
            'unit' => $unit,
            'class' => $class,
            'ManufacturerName' => $manuname,
            'ManufacturerPartID' => $manu_id,
            'line' => $line,
            'quantity' => $qty,
            'category' => $catagory,
            'sub_category' => $subcatagory,
        ];

        $url = "https://https-ats.amazonsedi.com/803f01f5-11e4-47df-b868-bb908211e0ed";
        $xml =
            '<?xml version="1.0" encoding="UTF-8"?>
      <!DOCTYPE cXML SYSTEM "http://xml.cXML.org/schemas/cXML/1.2.011/cXML.dtd">

      <cXML timestamp="2022-08-12" payloadID="' . $uniq . '" version="1.2.011">
  <Header>
        <From>
            <Credential domain="networkid">
                <Identity>NitrousTest5528363391</Identity>
                <Extrinsic  name="Email">tech@moshecom.com</Extrinsic>

            </Credential>
        </From>
        <To>
            <Credential domain="networkid">
                <Identity>Amazon</Identity>
            </Credential>
        </To>
        <Sender>
            <Credential domain="networkid">
                <Identity>NitrousTest5528363391</Identity>
                <SharedSecret>CvQ585ZrwyElwlDEETPFpwjOTMaav5</SharedSecret>
            </Credential>
            <UserAgent>test</UserAgent>
        </Sender>
        <Punchout>https://www.amazon.com/eprocurement/punchout</Punchout> 
    </Header>
    <Request deploymentMode="test">
        <OrderRequest>
        <SuplierSetup>
                <URL> http://userd8ff39619b0e21c.app.vtxhub.com/callback.php </URL>
                </SuplierSetup>
            <OrderRequestHeader orderDate="' . $date . '" orderID="' . $orderID . '" type="new" orderType="regular">
                <Total>
                    <Money currency="' . $currency . '">' . $money . '</Money>
                </Total>
                <ShipTo>
                    <Address isoCountryCode=" ' . $countrycode . '" addressID="' . $addressID . '">
                        <Name xml:lang="en-US">' . $org_name . '</Name>
                        <PostalAddress name="' . $name . '">
                            <DeliverTo>' . $deli1 . '</DeliverTo>
                            <Street>' . $street . '</Street>
                            <City>' . $city . '</City>
                            <State>' . $state . '</State>
                            <PostalCode>' . $postcode . '</PostalCode>
                            <Country  isoCountryCode=" ' . $countrycode . '">' . $country_name . '</Country>
                        </PostalAddress>
                        <Email name="' . $name . '">' . $email . '</Email>
                        <Phone name="' . $name . '">
                            <TelephoneNumber>
                                <CountryCode isoCountryCode=" ' . $countrycode . '">' . $country . '</CountryCode>
                                <AreaOrCityCode>' . $area_code . '</AreaOrCityCode>
                                <Number>' . $ph_no . '</Number>
                            </TelephoneNumber>
                        </Phone>
                        <Fax name="' . $fax_name . '">
                            <TelephoneNumber>
                                <CountryCode isoCountryCode=" ' . $countrycode . '">' . $country . '</CountryCode>
                                <AreaOrCityCode>' . $area_code . '</AreaOrCityCode>
                                <Number>' . $ph_no . '</Number>
                            </TelephoneNumber>
                        </Fax>
                    </Address>
                </ShipTo>
                <BillTo>
                    <Address isoCountryCode=" ' . $countrycode . '" addressID="' . $addressID . '">
                        <Name xml:lang="en-US">Worldwey</Name>
                        <PostalAddress name="' . $name . '">
                             <DeliverTo>' . $deli1 . '</DeliverTo>
                            <Street>' . $street . '</Street>
                            <City>' . $city . '</City>
                            <State>' . $state . '</State>
                            <PostalCode>' . $postcode . '</PostalCode>
                            <Country isoCountryCode=" ' . $countrycode . '">' . $country . '</Country>
                            <Email name="Email">' . $email . '</Email>
                             <Extrinsic name="Email">' . $email . '</Extrinsic>
                             <Extrinsic name="UserEmail">' . $email . '</Extrinsic>
                            <Phone name="work">
                            <TelephoneNumber>
                                <CountryCode isoCountryCode=" ' . $countrycode . '">' . $country . '</CountryCode>
                               <AreaOrCityCode>' . $area_code . '</AreaOrCityCode>
                                <Number>' . $ph_no . '</Number>
                            </TelephoneNumber>
                          </Phone>
                        </PostalAddress>
                        <Fax name="' . $fax_name . '">
                            <TelephoneNumber>
                                <CountryCode isoCountryCode=" ' . $countrycode . '">' . $country . '</CountryCode>
                                 <AreaOrCityCode>' . $area_code . '</AreaOrCityCode>
                                <Number>' . $ph_no . '</Number>
                            </TelephoneNumber>
                        </Fax>
                    </Address>
                </BillTo>
                <contact>
                    <name xml:lang="en-US">' . $name . '</name>
                    <phone>' . $ph_no . '</phone>
                    <Email>' . $email . '</Email>
                    <Email name="Email">' . $email . '</Email>
                </contact>
                <Shipping>
                    <Money currency="' . $currency . '">' . $money . '</Money>
                    <Description xml:lang="en">std-us</Description>
                </Shipping>
                <Tax>
                    <Money currency="' . $currency . '">' . $tax . '</Money>
                    <Description xml:lang="en">Included</Description>
                </Tax>
                <Comments>' . $comments . '</Comments>
                <Extrinsic name="Email">' . $email . '</Extrinsic>
                <Extrinsic name="UserEmail">' . $email . '</Extrinsic>
               
                <Email>tech@moshecom.com</Email>
            </OrderRequestHeader>
            <ItemOut quantity="' . $qty . '" lineNumber="' . $line . '">
          
                <ItemID>
                    <SupplierPartID>' . $asin . '</SupplierPartID>
                    <supplierPartAuxiliaryID> ' . $offer . '</supplierPartAuxiliaryID>
                </ItemID>
                <ItemDetail>
                    <UnitPrice>
                        <Money currency="' . $currency . '">59.99</Money>
                    </UnitPrice>
                    <Description xml:lang="en-US">' . $item_description . '</Description>
                    <UnitOfMeasure>' . $unit . '</UnitOfMeasure>
                    <Classification domain="UNSPSC">43201803</Classification>
                    <ManufacturerPartID>' . $manu_id . '</ManufacturerPartID>
                    <ManufacturerName>' . $manuname . '</ManufacturerName>
                    <Extrinsic name="soldBy">Amazon</Extrinsic>
                    <Extrinsic name="fulfilledBy">Amazon</Extrinsic>
                    <Extrinsic name="category">' . $catagory . '</Extrinsic>
                    <Extrinsic name="subCategory"> ' . $subcatagory . '</Extrinsic>
                    <Extrinsic name="itemCondition">New</Extrinsic>
                    <Extrinsic name="qualifiedOffer">true</Extrinsic>
                    <Extrinsic name="preference">default</Extrinsic>
                </ItemDetail>
            </ItemOut>
        </OrderRequest>
    </Request>
</cXML>';

        $headers = array(
            "Content-type: text/xml",
            "Content-length: " . strlen($xml),
            "Connection: close",
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $data = curl_exec($ch);
        $send = [$data, $base, $xml];
       
        return $send;
        if (curl_errno($ch)) {
            print curl_error($ch);
            Log::warning("Something Went Wrong In B-OrderAPI");
        } else {

            curl_close($ch);
        }
    }
}
