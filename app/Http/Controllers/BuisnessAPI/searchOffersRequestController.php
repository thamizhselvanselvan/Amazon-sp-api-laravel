<?php

namespace App\Http\Controllers\BuisnessAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AWS_Business_API\AWS_POC\searchOffersRequest;

class searchOffersRequestController extends Controller
{
    public function index()
    {
        return View('buisnessapi.search_Offers_request.index');
    }
    public function searchoffersproduct(Request $request)
    {
        $ApiCall = new searchOffersRequest();
        $data = $ApiCall->getAsinoff($request->asin);

        return response()->json(['success' => 'Asin details3 successfully Fetched', $data]);
    }
}
