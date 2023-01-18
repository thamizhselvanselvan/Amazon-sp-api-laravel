<?php

namespace App\Http\Controllers\BuisnessAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AWS_Business_API\AWS_POC\Search_Product_Request;

class SearchProductRequestController extends Controller
{
    public function index()
    {
        return View('buisnessapi.search_product_request.index');
    }
    public function searchproductRequest(Request $request)
    {   
        $search_words = '';
        $type = "key"; 

        if($request->has('asin')) {
            $type = "asin";
            $search_words = $request->data['asin'];
        } else {
            $search_words = $request->data['key'];
        }


        $ApiCall = new Search_Product_Request();
        $data = $ApiCall->getAsin($search_words, $type);
        
        return response()->json(['success' => ' details successfully Fetched', $data]);
    }
}
