<?php

namespace App\Http\Controllers\BuisnessAPI;

use App\Http\Controllers\Controller;
use App\Models\Catalog\AsinSource;
use Illuminate\Http\Request;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;
use App\Services\AWS_Business_API\Details_dump\product_details;

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
    // public function fetchusasin()
    // {
    //     $data = AsinSource::select('asin')
    //         ->where('source', 'US')
    //         ->get();
    //     foreach ($data as $val) {
    //         $fetched[] = ($val->asin);
    //     }

    //     $tes = new product_details;
    //     $tes->savedetails($fetched);
    // }
}
