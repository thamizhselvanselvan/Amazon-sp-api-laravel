<?php

namespace App\Services\Catalog;

use App\Services\BB\PushAsin;
use Illuminate\Support\Facades\Log;


class CsvAsinImport
{
    public function AsinImport($records)
    {

        $count = 0;
        $country_code = $records['source'];
        $country_code_lr = strtolower($country_code);
        $source = $records['source'];
        $priority = isset($records['priority']) ? $records['priority'] : '';

        $module = $records['module'];
        $source_lists = buyboxCountrycode();
        $product_lowest_price = [];
        $product = [];

        $model_name = table_model_create(country_code: $country_code_lr, model: "Asin_${module}", table_name: "asin_${module}_");
        $des_priority = $records['module'] == "destination" ? ["priority" => $records['priority']] : [];

        foreach ($records['ASIN'] as $asin) {

            $asin_details[] = [
                'asin' => $asin,
                'user_id' => $records['user_id'],
                ...$des_priority
            ];

            if ($records['module'] == "destination") {

                $product[] = [
                    'seller_id' => $source_lists[$source],
                    'active' => 1,
                    'asin1' => $asin,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $product_lowest_price[] = [
                    'asin' => $asin,
                    'cyclic' => 0,
                    'delist' => 0,
                    'available' => 0,
                    'priority'  => $priority,
                    'import_type' => 'Seller'
                ];
            }

            if ($count == 2000) {

                if ($records['module'] == "destination") {
                    $push_to_bb = new PushAsin();
                    $push_to_bb->PushAsinToBBTable(product: $product, product_lowest_price: $product_lowest_price, country_code: $source, priority: $priority);

                    $product = [];
                    $product_lowest_price = [];
                }

                $model_name->upsert($asin_details, ['user_asin_unique'], ['asin', 'user_id']);
                $count = 0;
                $asin_details = [];
            }
            $count++;
        }

        if ($records['module'] == "destination") {
            $push_to_bb = new PushAsin();
            $push_to_bb->PushAsinToBBTable(product: $product, product_lowest_price: $product_lowest_price, country_code: $source, priority: $priority);

            $product = [];
            $product_lowest_price = [];
        }

        $model_name->upsert($asin_details, ['user_asin_unique'], ['asin', 'user_id']);
        $asin_details = [];
    }
}