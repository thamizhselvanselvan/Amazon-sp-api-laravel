<?php

namespace App\Http\Controllers\Seller;

use RedBeanPHP\R;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\SP_API\API\Catalog;
use Illuminate\Support\Facades\Auth;
use App\Models\seller\AsinMasterSeller;
use Illuminate\Support\Facades\Artisan;

class SellerCatalogController extends Controller
{
  public function index()
  {
     $login_user = Auth::user();
    $seller_id = $login_user->bb_seller_id;
    $email = $login_user->email;

      if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

        $base_path = base_path();
        $command = "cd $base_path && php artisan pms:seller-catalog-import $seller_id > /dev/null &";
        exec($command);
    } else {

      Log::info($seller_id);
        Artisan::call('pms:seller-catalog-import ' .$seller_id);
    }
    Log::alert("working on click");
   
    // // Log::warning($datas[0]->asin);
    //   $chunk = 10;

    // // $datas = AsinMasterSeller::limit(10)->offset(0)->where('status', 0)->where('seller_id', $login_id)->get();
    // $datas = AsinMasterSeller::chunk($chunk)->where('status', 0)->where('seller_id', $login_id)->get();

    // $catalog =   new Catalog();
    // $catalogApi = $catalog->index($datas, $login_user);

  }
  
}
