<?php

namespace App\Http\Controllers\ProductPricing;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\ProductPricing\itemOffer;
use App\Services\getItemOffers;

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
        $asins = preg_split("/\r\n| |'|:|,/", $request->asin_values, -1, PREG_SPLIT_NO_EMPTY);

        $item_condition = $request->item_condition; //New, Used, Collectible, Refurbished, Club

        $marketplace_id = 'A21TJRUUN4KGV';
       
        foreach($asins as $asin)
        {
            $get_item_offers = new getItemOffers;
            $response = $get_item_offers->itemOffers($marketplace_id, $item_condition, $asin);
           
            // $response= $response->getOffers();
            
            foreach(json_decode($response) as $key => $value)
            {
                $data = "";

                $key = strtolower(lcfirst($key));

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
                echo $data ;

                // $product->{$key} = $data;

                echo "<HR>";

            }
       
        

        // $get_item_offers = new getItemOffers;
        //     $response = $get_item_offers->itemOffers($marketplace_id, $item_condition, $asins);
        //     echo "<pre>";
        //     print_r($response);
    }

   
}

}
