<?php

namespace App\Http\Controllers\BuisnessAPI;

use App\Http\Controllers\Controller;
use App\Models\MongoDBBusiness\Product_Details;
use Illuminate\Http\Request;

class ProductDetailsController extends Controller
{
   public function index()
   {
      // $data = Product_Details::query()
      //    ->where('asin', 'B00002N6SG')
      //    ->get()->toArry();
      // $dat = json_encode(json_decode($data[]));
      // dd($data);
      return View('buisnessapi.product_view.index');
   }
   public function viewpro(Request $request)
   {
      if ($request->ajax()) {
         
         $asin = ($request->asin);
         
         
         $data = Product_Details::query()
         ->where('asin', $asin)
         ->get();
         return response()->json(['success' => true, "data" => $data]);
        
      }
   }
}
