<?php

namespace App\Services\BB;

use Exception;
use App\Models\Mws_region;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SP_API\API\NewCatalog;

class PushAsin
{
    public function PushAsinToBBTable($product, $product_lowest_price, $country_code, $priority)
    {
        Log::alert('update into bb');

        $country_code = strtolower($country_code);
        $product_table = "product_aa_custom_p${priority}_${country_code}";
        $bb_product = table_model_set(country_code: $country_code, model: 'bb_product_aa_custom', table_name: $product_table);
        $bb_product->upsert($product, ['asin1'], ['asin1', 'seller_id', 'active', 'created_at', 'updated_at']);

        $lp_table = "product_aa_custom_p${priority}_${country_code}_offer";
        $bb_product_lowest_price = table_model_set(country_code: $country_code, model: 'bb_product_aa_custom_offer', table_name: $lp_table);
        $bb_product_lowest_price->upsert($product_lowest_price, ['asin'], ['asin', 'cyclic', 'delist', 'available', 'priority', 'import_type']);

        Log::alert("updated into buybox");
    }

    public function updateAsinInBB($asin, $country_code)
    {
        Log::alert("$asin updated into bb");
        $product[] = [
            'seller_id' => '40',
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
            'priority'  => '1',
            'import_type' => 'Seller'
        ];

        $this->PushAsinToBBTable($product, $product_lowest_price, $country_code, '1');
    }

    public function updateAsinSourceDestination($asin, $country_code)
    {
        Log::alert("$asin updated into soruce des");
        $model_name = table_model_create(country_code: $country_code, model: "Asin_source", table_name: "asin_source_");
        $model_name->upsert(
            [
                'asin' => $asin,
                'user_id' => '1',
                'status' => '0'
            ],
            ['user_asin_unique'],
            ['asin', 'user_id', 'status']
        );

        $model_name_des = table_model_create(country_code: $country_code, model: "Asin_destination", table_name: "asin_destination_");
        $model_name_des->upsert(
            [
                'asin' => $asin,
                'user_id' => '1',
                'status' => '0',
                'priority' => '1'
            ],
            ['user_asin_unique'],
            ['asin', 'user_id', 'status', 'priority']
        );
        Log::alert("$asin success into soruce des");
    }

    public function checkAsinAvailability($asin, $country_code, $aws_id, $error_title)
    {
        Log::info($asin);
        Log::alert($country_code);
        Log::critical($aws_id);
        try {
            $catalog_table_name = 'catalognew' . strtolower($country_code) . 's';
            $asins = DB::connection('catalog')->select("SELECT asin FROM $catalog_table_name where asin = '$asin' ");
            $country_code_up = strtoupper($country_code);

            Log::warning($asin);

            if (count($asins) <= 0) {
                $mws_regions = Mws_region::with(['aws_verified'])->where('region_code', $country_code_up)->get()->toArray();
                $aws_id_asin = $mws_regions[0]['aws_verified'][0]['id'];
                Log::notice($aws_id_asin);
                $asin_source[] = [
                    'asin' => $asin,
                    'seller_id' => $aws_id,
                    'source' => $country_code,
                    'id'    =>  $aws_id_asin,
                ];

                Log::info($asin_source);
                $this->updateAsinSourceDestination($asin, $country_code);
                $this->updateAsinInBB($asin, $country_code);
                (new NewCatalog())->Catalog($asin_source);
            }
        } catch (Exception $e) {
            $getMessage = $e->getMessage();
            $getCode = $e->getCode();
            $getFile = $e->getFile();
            $getLine = $e->getLine();

            $slackMessage = "Message: $getMessage 
            Code: $getCode,
            File: $getFile,
            Line: $getLine";
            Log::error($slackMessage);
            slack_notification('app360', $error_title, $slackMessage);
        }
        return true;
    }
}
