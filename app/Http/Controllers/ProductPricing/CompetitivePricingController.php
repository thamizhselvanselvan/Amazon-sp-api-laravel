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
        return view('productPricing.competitivePriceIndex');
    }


    public function create()
    {
    }



    public function show(Request $request)
    {
        $identity_values = preg_split("/\r\n| |'|:|,/", $request->identity_values, -1, PREG_SPLIT_NO_EMPTY);

        $marketplace_id = 'A21TJRUUN4KGV';
        $item_type = $request->identity_type; //'Asin'or Sku
        $asins = [];
        $skus = [];

        if ($item_type == 'Asin') {
            $asins = $identity_values;
        } else {
            $skus = $identity_values;
        }

        $get_competitive_pricing = new getCompetitivePricing;
        $response = $get_competitive_pricing->competitivePricing($marketplace_id, $item_type, $asins, $skus);
        echo "<pre>";
        print_r($response);
    }
}
