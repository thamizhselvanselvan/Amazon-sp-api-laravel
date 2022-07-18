<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\seller\AsinMasterSeller;
use Illuminate\Support\Facades\Response;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

Route::get("samsa/test", function () {

   $bb_user = User::where('bb_seller_id', '!=', NULL)->get('bb_seller_id');

   $chunk = 100;
   foreach ($bb_user as $user_id) {

      $seller_id = $user_id->bb_seller_id;
      AsinMasterSeller::where('seller_id', $seller_id)->chunk($chunk, function ($data) use($seller_id) {
         $country_code = strtolower($data[0]['source']);
         $asin = [];

         $product_lp = 'bb_product_lp_seller_detail_'.$country_code.'s';
         $product = 'bb_product_'.$country_code.'s';

         foreach ($data as $value) {
            $a = $value['asin'];
            $asin[] = "'$a'";
         }

         $asin = implode(',', $asin);
        $product_details = DB::connection('buybox')->select("SELECT 
         PPO.asin, PPO.is_fulfilled_by_amazon, PPO.listingprice_amount
         from $product as PP 
         join $product_lp as PPO
         ON PP.asin1 = PPO.asin
         WHERE PP.asin1 IN ($asin)
         AND PP.seller_id = 10 AND PPO.is_buybox_winner = 1
         " );
       
         po($product_details);

      });
   }
});
