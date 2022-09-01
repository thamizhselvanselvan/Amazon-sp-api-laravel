<?php

namespace App\Services\AWS_Business_API\AWS_POC;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;


class Orders
{
    public function getOrders()
    {
        $val = random_int(100, 10000);
        $random = substr(md5(mt_rand()), 0, 7);
        $uniq = $random . $val . '@moshecom.com';
        $date = Carbon::now()->format('d-m-Y');
        $countrycode = 'US';
        $country = 'USA';
        $country_name = 'Unite States';
        $email = 'tech@moshecom.com';
        $name = 'mr.kate';

        /* ship and bill details  */

       
        $orderID = random_int(100, 10000);
        $org_name = 'nitrous';
        $deli1 = 'Tech Team, tech@moshecom.com';
        $deli2 = 'bliss';
        $deli3 = 'Tech TEAM';
        $street = '325 9th Ave N';
        $city = 'Seattle';
        $state = 'Washington';
        $postcode = '98109';
        $area_code = '213';
        $ph_no = '87488423820';
        // $addressID = "[Bill To Code]";
        $fax_name = 'nitrous';
        $comments = 'Deliveery as soon as possible';
        $extrinsic = 'Nitrous';
        $supplierPartAuxiliaryID = random_int(10000, 1000000);
        $tax = '1';
        $currency = 'USD';
        /* item details  */
        $money  = 2.91;
        $asin = 'B09542G9ZN';
        $item_description = 'Amazon Basics Cotton Rounds, 100ct, 1-Pack (Previously Solimo)';
        $unit = 'EA';
        $class = '';
        $manuname = 'Amazon.com Services, Inc.';
        $manu_id = '';
        $subcatagory = '';
        $catagory = 'Plastic';
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
            //    'address_id' => $addressID,
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
            <UserAgent>Amazon Business cXML Application</UserAgent>
        </Sender>
        <Punchout>https://www.amazon.com/eprocurement/punchout</Punchout> 

    </Header>
    <Request deploymentMode="test">
        <OrderRequest>
            <OrderRequestHeader orderDate="' . $date . '" orderID=' . $orderID . ' type="new" orderType="regular">
                <Total>
                    <Money currency="' . $currency . '">' . $money . '</Money>
                </Total>
                <ShipTo>
                    <Address ' . $countrycode . ' addressID="Z4SEYWLA5FAVH5AB2MJMG05F1UA07299273BV7569MTU1FZPXTQ2EQA2OX24EF2">
                        <Name xml:lang="en-US">' . $org_name . '</Name>
                        <PostalAddress name=' . $name . '>
                            <DeliverTo>' . $deli1 . '</DeliverTo>
                              <DeliverTo>' . $deli2 . '</DeliverTo>
                            <DeliverTo>' . $deli3 . '</DeliverTo>
                            <Street>' . $street . '</Street>
                            <City>' . $city . '</City>
                            <State>' . $state . '</State>
                            <PostalCode>' . $postcode . '</PostalCode>
                            <Country ' . $countrycode . '">' . $country_name . '</Country>
                        </PostalAddress>
                        <Email name=' . $name . '>' . $email . '</Email>
                        <Phone name=' . $name . '>
                            <TelephoneNumber>
                                <CountryCode ' . $countrycode . '">' . $country . '</CountryCode>
                                <AreaOrCityCode>' . $area_code . '</AreaOrCityCode>
                                <Number>' . $ph_no . '</Number>
                            </TelephoneNumber>
                        </Phone>
                        <Fax name=' . $fax_name . '>
                            <TelephoneNumber>
                                <CountryCode ' . $countrycode . '>' . $country . '</CountryCode>
                                <AreaOrCityCode>' . $area_code . '</AreaOrCityCode>
                                <Number>' . $ph_no . '</Number>
                            </TelephoneNumber>
                        </Fax>
                    </Address>
                </ShipTo>
                <BillTo>
                    <Address ' . $countrycode . ' addressID="Z4SEYWLA5FAVH5AB2MJMG05F1UA07299273BV7569MTU1FZPXTQ2EQA2OX24EF2">
                        <Name xml:lang="en-US">Worldwey</Name>
                        <PostalAddress name=' . $name . '>
                             <DeliverTo>' . $deli1 . '</DeliverTo>
                            <Street>' . $street . '</Street>
                            <City>' . $city . '</City>
                            <State>' . $state . '</State>
                            <PostalCode>' . $postcode . '</PostalCode>
                            <Country ' . $countrycode . '>' . $country . '</Country>
                            <Email name=' . $name . '>' . $email . '</Email>
                            <Phone name="work">
                            <TelephoneNumber>
                                <CountryCode ' . $countrycode . '>' . $country . '</CountryCode>
                               <AreaOrCityCode>' . $area_code . '</AreaOrCityCode>
                                <Number>' . $ph_no . '</Number>
                            </TelephoneNumber>
                        </Phone>
                        <Fax name=' . $fax_name . '>
                            <TelephoneNumber>
                                <CountryCode ' . $countrycode . '>' . $country . '</CountryCode>
                                 <AreaOrCityCode>' . $area_code . '</AreaOrCityCode>
                                <Number>' . $ph_no . '</Number>
                            </TelephoneNumber>
                        </Fax>
                    </Address>
                </BillTo>
                <Shipping>
                    <Money currency="' . $currency . '">' . $money . '</Money>
                    <Description xml:lang="en">std-us</Description>
                </Shipping>
                <Tax>
                    <Money currency="' . $currency . '">' . $tax . '</Money>
                    <Description xml:lang="en">Included</Description>
                </Tax>
                <Comments>' . $comments . '</Comments>
                <Extrinsic name="Name">' . $extrinsic . '</Extrinsic>
            </OrderRequestHeader>
            <ItemOut quantity=' . $qty . ' lineNumber=' . $line . '>
            <Requested Delivery Date>2022-08-28</Requested>
                <ItemID>
                    <SupplierPartID>' . $asin . '</SupplierPartID>
                    <SupplierPartAuxiliaryID>' . $supplierPartAuxiliaryID . '</SupplierPartAuxiliaryID>
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
        $send = [$data, $base,$xml];

        return $send;
        if (curl_errno($ch))
            print curl_error($ch);
        else
            curl_close($ch);
    }
}
