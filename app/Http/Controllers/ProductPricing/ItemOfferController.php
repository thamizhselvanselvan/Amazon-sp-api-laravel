<?php

namespace App\Http\Controllers\ProductPricing;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\ProductPricing\itemOffer;
use App\Services\getItemOffers;
use \RedBeanPHP\R as R;

class ItemOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('productPricing.itemofferindex');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    
    public function show(Request $request)
    {   
        // $marketplace = 'A21TJRUUN4KGV';// india
        $marketplace = 'ATVPDKIKX0DER';// us
        
       $host = config('app.host');
       $port = config('app.port');
       $dbname = config('app.database');
       $username = config('app.username');
       $password = config('app.password');

       R::setup('mysql:host=' . $host . ';dbname=' . $dbname . ';port=' .$port, $username, $password);
       
       R::exec('TRUNCATE `itemoffer`');

        $asins = preg_split("/\r\n| |'|:|,/", $request->asin_values, -1, PREG_SPLIT_NO_EMPTY);

        $item_condition = $request->item_condition; //New, Used, Collectible, Refurbished, Club

        
       
        foreach($asins as $asin)
        {        
            $product = R::dispense("itemoffer");

            if(file_exists('offerpriceasin/'.$asin.'.txt'))
            { // read from file

                echo 'reading from file <BR>';

                $response = json_decode(file_get_contents('offerpriceasin/'.$asin.'.txt'));

                

                foreach (($response) as $key => $value) {

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

                //read from file
            }  
            
            else{

            $marketplace_id = 'ATVPDKIKX0DER';
            $get_item_offers = new getItemOffers;
            $response = $get_item_offers->itemOffers($marketplace_id, $item_condition, $asin);

            if($response)
            {
                echo 'reading from amz';
                file_put_contents('offerpriceasin/'.$asin.'.txt', Json_encode(Json_decode($response)));

                foreach(json_decode($response) as $key => $value)
                {
                    $data = "";
                    
    
                    $key = strtolower(lcfirst($key));
    
                    print_r("Key:" . $key);
                    echo "<BR>";
                    echo "Value: ";
    
    
                    if (is_array($value)) {
    
                        $data = (json_encode($value));
                       
    
                    } else if (is_object($value)) {
    
                       
                         $temp = (array) $value;
                        $data = json_encode($temp);
                       
    
                    } else {
    
                        $data =  $value;
                        
                    }
                    echo $data ;
                   
    
                    $product->{$key} = $data;
    
                    echo "<HR>";
    
                }
                // R::store($product);

                sleep(1);
            }
            else
            {
                echo 'no';
            }
            
           
       


        }

            
        

        // $get_item_offers = new getItemOffers;
        //     $response = $get_item_offers->itemOffers($marketplace_id, $item_condition, $asins);
        //     echo "<pre>";
        //     print_r($response);
    }

   
}

}
