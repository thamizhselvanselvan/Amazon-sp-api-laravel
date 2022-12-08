<?php

namespace App\Services\BB;

use Illuminate\Support\Facades\Log;

class PushAsin
{
    public function PushAsinToBBTable($product, $product_lowest_price, $country_code, $priority)
    {
        $country_code = strtolower($country_code);
        $product_table = "product_aa_custom_p${priority}_${country_code}";
        $bb_product = table_model_set(country_code: $country_code, model: 'bb_product_aa_custom', table_name: $product_table);
        $bb_product->upsert($product, ['asin1'], ['asin1', 'seller_id', 'active', 'created_at', 'updated_at']);

        $lp_table = "product_aa_custom_p${priority}_${country_code}_offer";
        $bb_product_lowest_price = table_model_set(country_code: $country_code, model: 'bb_product_aa_custom_offer', table_name: $lp_table);
        $bb_product_lowest_price->upsert($product_lowest_price, ['asin'], ['asin', 'cyclic', 'delist', 'available', 'priority', 'import_type']);
    }
}
