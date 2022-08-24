<?php

namespace App\Services\BB;

use Illuminate\Support\Facades\Log;

class PushAsin
{
    public function PushAsinToBBTable($product, $product_lowest_price, $country_code)
    {
        // Log::alert($country_code);
        $country_code = strtolower($country_code);
        $bb_product = table_model_set($country_code, 'BB_Product', 'product');
        $bb_product->insert($product);

        $lp_table = "product_${country_code}s_lp_offer";

        $bb_product_lowest_price = table_model_set(country_code: $country_code, model: 'BB_Product_lowest_price_offer', table_name: $lp_table);
        $bb_product_lowest_price->upsert($product_lowest_price, ['asin'], ['asin']);
    }
}
