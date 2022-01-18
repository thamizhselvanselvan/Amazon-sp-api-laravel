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
        //
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

    
    public function show()
    {
        $marketplace_id = 'A21TJRUUN4KGV';
        $item_condition='New';
        $asins = 'B07WMS7TWB';
        

        $get_item_offers = new getItemOffers;
            $response = $get_item_offers->itemOffers($marketplace_id, $item_condition, $asins);
            echo "<pre>";
            print_r($response);
    }

   
}
