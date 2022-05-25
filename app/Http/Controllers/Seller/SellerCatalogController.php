<?php

namespace App\Http\Controllers\Seller;

use RedBeanPHP\R;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
    $login_id = $login_user->id;
    $email = $login_user->email;

      if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

        $base_path = base_path();
        $command = "cd $base_path && php artisan pms:seller-catalog-import $login_id $email > /dev/null &";
        exec($command);
    } else {

        Artisan::call('pms:seller-catalog-import ' .$login_id.' '.$email);
    }

   
    // // Log::warning($datas[0]->asin);
    //   $chunk = 10;

    // // $datas = AsinMasterSeller::limit(10)->offset(0)->where('status', 0)->where('seller_id', $login_id)->get();
    // $datas = AsinMasterSeller::chunk($chunk)->where('status', 0)->where('seller_id', $login_id)->get();

    // $catalog =   new Catalog();
    // $catalogApi = $catalog->index($datas, $login_user);

  }
}
