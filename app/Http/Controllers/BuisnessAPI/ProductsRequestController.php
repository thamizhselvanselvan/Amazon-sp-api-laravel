<?php

namespace App\Http\Controllers\BuisnessAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;

class ProductsRequestController extends Controller
{
    public function index()
    {
        return View('buisnessapi.product_request.index');
    }
    public function productRequestasin(Request $request)
    {
        $ApiCall = new ProductsRequest();
        $data = $ApiCall->getASINpr($request->asin);

        return response()->json(['success' => ' details successfully Fetched', $data]);
    }
}
