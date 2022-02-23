<?php

namespace App\Http\Controllers\product;

use Exception;
use League\Csv\Reader;
use RedBeanPHP\R as R;
use League\Csv\Statement;
use Illuminate\Http\Request;
use League\Csv\XMLConverter;
use SellingPartnerApi\Endpoint;
use App\Http\Controllers\Controller;
use SellingPartnerApi\Configuration;
use Maatwebsite\Excel\Concerns\ToArray;
use SellingPartnerApi\Api\CatalogItemsV0Api as CatalogItemsV0ApiPackage;


class productController extends Controller
{
    public function index(){

        return view('product.index');
    }

public function fetchFromAmazon(){

// See README for more information on the Configuration object's options

$clientId = 'amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf';
$clientSecret = '5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765';
$refreshToken = 'Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg';
$awsAccessKeyId = 'AKIAZTIHMXYBD5SRG5IZ';
$awsSecretAccessKey = '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';
$endpoint = Endpoint::NA;

$config = new Configuration([
    "lwaClientId" => $clientId,
    "lwaClientSecret" => $clientSecret,
    "lwaRefreshToken" => $refreshToken,
    "awsAccessKeyId" => $awsAccessKeyId,
    "awsSecretAccessKey" => $awsSecretAccessKey,
    "endpoint" => $endpoint,  // or another endpoint from lib/Endpoints.
    "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role',
]);
    $records =[];
    $csv = Reader::createFromPath('C:/Users/Dell/Dropbox/PMS/Moshecom/ASIN Database.txt', 'r');
    $csv->setDelimiter("\t");
    $csv->setHeaderOffset(0);

    $stmt = (new Statement())
        ->where(function (array $record) {
            return $record;
            })
        ->offset(51)
        ->limit(1000);
    
    $records = $stmt->process($csv);

    $dataArray = [];

    R::setup('mysql: host=localhost; dbname=sp-api', 'root', 'root');   
    // R::exec('TRUNCATE `productcatalogs`'); 

    foreach($records as $record)
    {   
        $apiInstance = new CatalogItemsV0ApiPackage($config);
        $marketplace_id = 'ATVPDKIKX0DER'; // string | A marketplace identifier. Specifies the marketplace for the item.
        $asin = $record['asin']; // string | The Amazon Standard Identification Number (ASIN) of the item.
        
        try {
            $result = $apiInstance->getCatalogItem($marketplace_id, $asin);
            echo "<pre>";
            $result = json_decode(json_encode($result));
            
            $result = (array)($result->payload->AttributeSets[0]);
            
                $productcatalogs = R::dispense('productcatalogs');
        
            $value = [];
        
            foreach ($result as $key => $data){
                
                $key = lcfirst($key);
                if(is_object($data)){
        
                    $productcatalogs->{$key} = json_encode($data);
        
                    // $objvalues = json_encode($data);
                    // $value [][$key] = ($objvalues);
                    
                    // foreach($objvalues as $objkey => $objvalue){
                    //     if(is_object($objvalue)) {$objvalues1 = json_decode(json_encode($objvalue));
                    //       foreach($objvalues1 as $objkey1 => $objvalue1)
                    //     {$value [][$key.'_'.$objkey.'_'.$objkey1] = ($objvalue1);}} else{ $value [][$objkey] = ($objvalue);}}
                }
                else
                {
                    $productcatalogs->{$key} = json_encode($data);
                    // $value [][$key] = ($data);
                }
            }
            R::store($productcatalogs);
            
        
            } catch (Exception $e) {
                echo 'Exception when calling CatalogItemsV0Api->getCatalogItem: ', $e->getMessage(), PHP_EOL;
            }
    }
     
    }
}
