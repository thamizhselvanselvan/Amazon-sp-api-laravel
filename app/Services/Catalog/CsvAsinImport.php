<?php

namespace App\Services\Catalog;

use Illuminate\Support\Facades\Log;


class CsvAsinImport
{
    public function AsinImport($records)
    {
        $count = 0;
        $country_code = strtolower($records['source']);
        foreach ($records['ASIN'] as $record) {

            $asin_details[] = [
                'asin' => $record,
                'user_id' => $records['user_id'],
            ];

            if ($count == 2500) {

                $model_name = table_model_create(country_code: $country_code, model: 'Asin_source', table_name: 'asin_source_');
                $model_name->upsert($asin_details, ['user_asin_unique'], ['asin', 'user_id']);
                $count = 0;
                $asin_details = [];
            }
            $count++;
        }
        $table_name = table_model_create(country_code: $records['source'], model: 'Asin_source', table_name: 'asin_source_');
        $table_name->upsert($asin_details, ['user_asin_unique'], ['asin', 'user_id']);
    }
}
