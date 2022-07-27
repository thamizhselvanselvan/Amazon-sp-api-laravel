<?php

namespace App\Services\AWS_Business_API\AWS_POC;

use Exception;
use Illuminate\Support\Facades\Storage;


class Orders
{
    public function getOrders()
    {

        $url = "https://https-ats.amazonsedi.com/803f01f5-11e4-47df-b868-bb908211e0ed";
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
      <!DOCTYPE cXML SYSTEM "http://xml.cXML.org/schemas/cXML/1.2.011/cXML.dtd">

      <cXML timestamp="2017-06-27" payloadID="1498558305994.110967.712@amazon.com" version="1.2.011">
  <Header>
        <From>
            <Credential domain="networkid">
                <Identity>NitrousTest5528363391</Identity>
            </Credential>
        </From>
        <To>
            <Credential domain="networkid">
                <Identity>NitrousTest5528363391</Identity>
            </Credential>
        </To>
        <Sender>
            <Credential domain="networkid">
                <Identity>NitrousTest5528363391</Identity>
                <SharedSecret>CvQ585ZrwyElwlDEETPFpwjOTMaav5</SharedSecret>
            </Credential>
            <UserAgent>Amazon Business cXML Application</UserAgent>
        </Sender>
    </Header>
    <Request deploymentMode="test">
        <OrderRequest>
            <OrderRequestHeader orderDate="2022-07-25" orderID="TMHTZXLDFN" type="new" orderType="regular">
                <Total>
                    <Money currency="USD">59.99</Money>
                </Total>
                <ShipTo>
                    <Address isoCountryCode="US" addressID="Z4SEYWLA5FAVH5AB2MJMG05F1UA07299273BV7569MTU1FZPXTQ2EQA2OX24EF2">
                        <Name xml:lang="en-US">Org Name</Name>
                        <PostalAddress name="default">
                            <DeliverTo>Nile Team, nile-team@amazon.com</DeliverTo>
                            <DeliverTo>BISS</DeliverTo>
                            <DeliverTo>Nile Team</DeliverTo>
                            <Street>325 9th Ave N</Street>
                            <City>Seattle</City>
                            <State>WA</State>
                            <PostalCode>98109</PostalCode>
                            <Country isoCountryCode="US">United States</Country>
                        </PostalAddress>
                        <Email name="default">sram868+123@gmail.com</Email>
                        <Phone name="work">
                            <TelephoneNumber>
                                <CountryCode isoCountryCode="US">US</CountryCode>
                                <AreaOrCityCode>213</AreaOrCityCode>
                                <Number>1234567890</Number>
                            </TelephoneNumber>
                        </Phone>
                        <Fax name="default">
                            <TelephoneNumber>
                                <CountryCode isoCountryCode="Code">Country Code</CountryCode>
                                <AreaOrCityCode>Area Code</AreaOrCityCode>
                                <Number>098765432</Number>
                            </TelephoneNumber>
                        </Fax>
                    </Address>
                </ShipTo>
                <BillTo>
                    <Address isoCountryCode="+1" addressID="Z4SEYWLA5FAVH5AB2MJMG05F1UA07299273BV7569MTU1FZPXTQ2EQA2OX24EF2">
                        <Name xml:lang="en-US">Worldwey</Name>
                        <PostalAddress name="default">
                            <DeliverTo>no 7</DeliverTo>
                            <Street>2nd street</Street>
                            <City>Chicago</City>
                            <State>IL</State>
                            <PostalCode>60009</PostalCode>
                            <Country isoCountryCode="US">USA</Country>
                        </PostalAddress>
                        <Email name="default">test@gmail.com</Email>
                        <Phone name="work">
                            <TelephoneNumber>
                                <CountryCode isoCountryCode="+1">US</CountryCode>
                                <AreaOrCityCode>217</AreaOrCityCode>
                                <Number>123456788</Number>
                            </TelephoneNumber>
                        </Phone>
                        <Fax name="default">
                            <TelephoneNumber>
                                <CountryCode isoCountryCode="+1">US</CountryCode>
                                <AreaOrCityCode>217</AreaOrCityCode>
                                <Number>1234567899</Number>
                            </TelephoneNumber>
                        </Fax>
                    </Address>
                </BillTo>
                <Shipping>
                    <Money currency="USD">0</Money>
                    <Description xml:lang="en">std-us</Description>
                </Shipping>
                <Tax>
                    <Money currency="USD">1.00</Money>
                    <Description xml:lang="en">Included</Description>
                </Tax>
                <Comments>something inside nothing</Comments>
                <Extrinsic name="Name">Value for Header Level Extrinsic</Extrinsic>
            </OrderRequestHeader>
            <ItemOut quantity="1" lineNumber="1">
                <ItemID>
                    <SupplierPartID>B01LWRTRZU</SupplierPartID>
                    <SupplierPartAuxiliaryID>144-6522680-6556620,1</SupplierPartAuxiliaryID>
                </ItemID>
                <ItemDetail>
                    <UnitPrice>
                        <Money currency="USD">59.99</Money>
                    </UnitPrice>
                    <Description xml:lang="en-US">Seagate Firecuda Gaming 1TB 2.5-Inch SATA 6GB/s 5400rpm 128 MB Cache Internal Hard Drive (ST1000LX015)</Description>
                    <UnitOfMeasure>EA</UnitOfMeasure>
                    <Classification domain="UNSPSC">43201803</Classification>
                    <ManufacturerPartID>UPC-763649069073</ManufacturerPartID>
                    <ManufacturerName>Seagate</ManufacturerName>
                    <Extrinsic name="soldBy">Amazon</Extrinsic>
                    <Extrinsic name="fulfilledBy">Amazon</Extrinsic>
                    <Extrinsic name="category">gl_pc</Extrinsic>
                    <Extrinsic name="subCategory">COMPUTER_DRIVE_OR_STORAGE</Extrinsic>
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
        return $data;
        if (curl_errno($ch))
            print curl_error($ch);
        else
            curl_close($ch);
    }
}
