<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SP_API\API\Catalog;

class SellerCatalogController extends Controller
{
    public function index()
    {
      $catalog =   new Catalog();
      $catalogApi = $catalog->getCatalog();
      
    }
   
}
