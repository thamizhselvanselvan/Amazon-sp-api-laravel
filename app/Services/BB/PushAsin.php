<?php

namespace App\Services\BB;

class PushAsin
{
    public function PushAsinToBBTable($product, $product_lowest_price, $country_code)
    {
        $bb_product = table_model_set($country_code, 'BB_Product', 'product');
        $bb_product->insert($product);

        $bb_product_lowest_price = table_model_set(country_code: $country_code, model: 'BB_Product_lowest_price_offer', table_name: 'product_lp_offer');
        $bb_product_lowest_price->upsert($product_lowest_price, ['asin'], ['asin']);


        //
    }
}
