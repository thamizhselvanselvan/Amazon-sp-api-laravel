<?php

namespace App\Services\BB;

use Illuminate\Support\Facades\Log;

class PushAsin
{
    public function PushAsinToBBTable($product, $product_lowest_price, $country_code, $priority)
    {
        $country_code = strtolower($country_code);
        $product_table = "product_aa_custom_p${priority}_${country_code}";
        // $bb_product = table_model_set($country_code, 'BB_Product', 'product');
        $bb_product = table_model_set(country_code: $country_code, model: 'bb_product_aa_custom', table_name: $product_table);
        $bb_product->insert($product);

        // $lp_table = "product_${country_code}s_lp_offer";
        // $bb_product_lowest_price = table_model_set(country_code: $country_code, model: 'BB_Product_lowest_price_offer', table_name: $lp_table);

        $lp_table = "product_aa_custom_p${priority}_${country_code}_offer";
        $bb_product_lowest_price = table_model_set(country_code: $country_code, model: 'bb_product_aa_custom_offer', table_name: $lp_table);
        $bb_product_lowest_price->upsert($product_lowest_price, ['asin'], ['asin', 'cyclic', 'priority', 'import_type']);
    }
}
