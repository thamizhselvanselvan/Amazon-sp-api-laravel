<?php

namespace App\Http\Controllers\BuisnessAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;
use App\Services\AWS_Business_API\AWS_POC\Search_Product_Request;

class SearchProductRequestController extends Controller
{
    public function index()
    {
        $ApiCall = new Search_Product_Request();
        $data = $ApiCall->getAsin();
        dd($data);
        exit;
        return View('buisnessapi.search_product_request.index');
    }
    public function searchproductRequest(Request $request)
    {
        $ApiCall = new Search_Product_Request();
        $data = $ApiCall->getAsin($request->asin);

        return response()->json(['success' => 'Asin details successfully Fetched', $data]);
    }
}
