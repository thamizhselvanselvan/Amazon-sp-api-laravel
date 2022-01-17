<?php

namespace App\Http\Controllers;

use App\Services\SpApi;
use App\Models\Products;
use App\Models\SaveAsin;
use ClouSale\AmazonSellingPartnerAPI\Models\FbaSmallAndLight\MarketplaceId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ClouSale\AmazonSellingPartnerAPI\Models\ProductPricing\Product;
use Illuminate\Support\Arr;
use PhpParser\JsonDecoder;
use \RedBeanPHP\R as R;

class SaveAsinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('show');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request)
    {
        $marketplace = 'A21TJRUUN4KGV';

        R::setup('mysql:host=localhost;dbname=spapi', 'root', 'root');

        $titles = preg_split("/\r\n| |''|,/", $request->asinText);

        
        R::exec('TRUNCATE `product`');

        $newData = [];

        foreach ($titles as $title) {

            $product = R::dispense("product");

            if (!empty(trim($title))) {

                $title = trim($title);
             
                if (file_exists($title.'.txt')) {

                    echo 'reading from file <BR>';
                   
                    $response = json_decode(file_get_contents($title.'.txt'));

                    foreach ($response->AttributeSets[0] as $key => $value) {

                        $data = "";

                        $key = lcfirst($key);

                        print_r("Key:" . $key);
                        echo "<BR>";
                        echo "Value: ";

                        if (is_array($value)) {

                            $data = json_encode($value);
                        } 
                        else if (is_object($value)) {

                            $temp = (array) $value;
                            $data = json_encode($temp);
                        } 
                        else {

                            $data =  $value;
                        }
                        echo $data;

                        $product->{$key} = $data;

                        echo "<HR>";
                    }
                    R::store($product);
             

            } 
            else {

            echo 'reading from amazon';
           

            $sp_api = new SpApi;
            $response = $sp_api->catalogApitest($marketplace, $title);


            file_put_contents($title.'.txt', Json_encode(Json_decode($response)));

            
            foreach (Json_decode($response)->AttributeSets[0] as $key => $value) {

                $data = "";

                $key = lcfirst($key);


                print_r("Key:" . $key);
                echo "<BR>";
                echo "Value: ";

                if (is_array($value)) {

                    $data = json_encode($value);
                } 
                else if (is_object($value)) {

                    $temp = (array) $value;
                    $data = json_encode($temp);
                } 
                else {

                    $data =  $value;
                }
                echo $data;

                $product->{$key} = $data;

                echo "<HR>";
            }

                R::store($product);

                sleep(2);

            }
        }
    }

        // $titles = preg_split("/\r\n| |''|,/", $request->asinText);
        // $newData = [];

        // foreach ($titles as $title) {
        //     if (!empty(trim($title))) {
        //         $newData[] = trim($title);
        //     }
        // }

        // echo "<PRE>";

        // // return $newData;
        // $count = 0;
        // $title = [];
        // $sp_api = new SpApi;

        // print_r($newData);

        // foreach ($newData as $data) {

        //     $count++;
        //     if ($count > 5) {
        //         break;
        //     }

        //     $response = $sp_api->catalogApitest($marketplace, $data);
        //     $product = $response->getAttributeSets();
        //     $product = $product[0];
        //     /*
        //     print_r($product->getTitle());
        //     print_r($product->getListPrice());
        //     print_r($product->getPackageDimensions()->getHeight()->getValue());
        //     print_r($product->getPackageDimensions()->getHeight()->getunits());
        //     */
        //     print_r($product);

        //     exit;
        //     sleep(2);

        //     //DB::table('products')->insert(['ASIN' => $data, 'Title' => $title]);

        //     // return $sp_api->catalogApitest($marketplace, $data);
        // }
    }
}
