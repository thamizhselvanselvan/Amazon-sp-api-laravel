<?php

namespace App\Http\Controllers;

use App\Models\ProductPricing\pricing;
use App\Services\SpApi;
use App\Models\Products;
use ClouSale\AmazonSellingPartnerAPI\Models\FbaSmallAndLight\MarketplaceId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ClouSale\AmazonSellingPartnerAPI\Models\ProductPricing\Product;
use Illuminate\Support\Arr;
use PhpParser\JsonDecoder;
use \RedBeanPHP\R as R;

class CatlogApiController extends Controller
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
        
       $host = config('app.host');
       $port = config('app.port');
       $dbname = config('app.database');
       $username = config('app.username');
       $password = config('app.password');

       R::setup('mysql:host=' . $host . ';dbname=' . $dbname . ';port=' .$port, $username, $password);
       

        $asins = preg_split("/\r\n| |''|,/", $request->asinText);


        R::exec('TRUNCATE `product`');

        foreach ($asins as $asin) {

            $product = R::dispense("product");

            if (!empty(trim($asin))) {

                $asin = trim($asin);

                if (file_exists($asin . '.txt')) {

                    echo 'reading from file <BR>';

                    $response = json_decode(file_get_contents($asin . '.txt'));

                    foreach ($response->AttributeSets[0] as $key => $value) {

                        $data = "";

                        $key = lcfirst($key);

                        print_r("Key:" . $key);
                        echo "<BR>";
                        echo "Value: ";

                        if (is_array($value)) {

                            $data = json_encode($value);
                        } else if (is_object($value)) {

                            $temp = (array) $value;
                            $data = json_encode($temp);
                        } else {

                            $data =  $value;
                        }
                        echo $data;

                        $product->{$key} = $data;

                        echo "<HR>";
                    }
                    R::store($product);
                } else {

                    


                    $sp_api = new SpApi;
                    $response = $sp_api->catalogApitest($marketplace, $asin);
                    echo 'reading from amazon';

                
                    // print_r($response);
                    // exit;
                    if($response){

                        file_put_contents($asin . '.txt', Json_encode(Json_decode($response)));


                        foreach (Json_decode($response)->AttributeSets[0] as $key => $value) {

                            $data = "";

                            $key = lcfirst($key);

                            print_r("Key:" . $key);
                            echo "<BR>";
                            echo "Value: ";

                            if (is_array($value)) {

                                $data = json_encode($value);
                            } else if (is_object($value)) {

                                $temp = (array) $value;
                                $data = json_encode($temp);
                            } else {

                                $data =  $value;
                            }
                            echo $data;

                            $product->{$key} = $data;

                            echo "<HR>";
                        }
                        R::store($product);
                        
                        sleep(2);
                    }
                    else
                    {
                        
                    }
                    
                }
            }
        }
    }
}
