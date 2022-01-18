<?php

namespace App\Http\Controllers\ProductPricing;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\getCompetitivePricing;
use App\Models\ProductPricing\CompetitivePricing;

class CompetitivePricingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    
    }

   
    public function create()
    {
    
    }

    
    
    public function show(Request $request)
    {
        $marketplace_id = 'A21TJRUUN4KGV';
        $item_type = 'Asin';
        $asins = 'B07WMS7TWB';
        $skus = '';

        $get_competitive_pricing = new getCompetitivePricing;
            $response = $get_competitive_pricing->competitivePricing($marketplace_id, $item_type, $asins);
            echo "<pre>";
            print_r($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductPricing\CompetitivePricing  $competitivePricing
     * @return \Illuminate\Http\Response
     */
    
    

    
}
