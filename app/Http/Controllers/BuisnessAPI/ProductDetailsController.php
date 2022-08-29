<?php

namespace App\Http\Controllers\BuisnessAPI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductDetailsController extends Controller
{
   public function index()
   {
        return View('buisnessapi.product_view.index');
   }
}
