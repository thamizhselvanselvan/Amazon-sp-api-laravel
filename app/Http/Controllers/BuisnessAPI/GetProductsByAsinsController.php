<?php

namespace App\Http\Controllers\BuisnessAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AWS_Business_API\AWS_POC\getProductsByAsins;

class GetProductsByAsinsController extends Controller
{
    public function index()
    {
        // $ApiCall = new getProductsByAsins();
        // $data = $ApiCall->getASINbyasin();
        // dd($data);
        return View('buisnessapi.get_products_byasins.index');
    }
    public function searchasinproduct(Request $request)
    {

        $ApiCall = new getProductsByAsins();
        $data = $ApiCall->getASINbyasin($request->asin);

        return response()->json(['success' => 'Asin details4 successfully Fetched', $data]);
    }
}
