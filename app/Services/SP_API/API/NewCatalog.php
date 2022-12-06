<?php

namespace App\Services\SP_API\API;

use config;
use Exception;
use RedBeanPHP\R;
use App\Models\Mws_region;
use App\Models\Aws_credential;
use App\Models\Catalog\Catalog;
use App\Models\Catalog\AsinSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Admin\ErrorReporting;
use App\Models\Catalog\Catalog_ae;
use App\Models\Catalog\Catalog_in;
use App\Models\Catalog\Catalog_sa;
use App\Models\Catalog\Catalog_us;
use App\Models\Catalog\CatalogMissingAsin;
use App\Services\SP_API\Config\ConfigTrait;
use SellingPartnerApi\Api\CatalogItemsV20220401Api;

class NewCatalog
{
    use ConfigTrait;

    public function Catalog($records, $seller_id = NULL)
    {
        Log::info('catalog import working');
        $queue_data = [];
        $upsert_asin = [];
        $country_code1 = '';
        $asins = [];
        $count = 0;
        $auth_id = '';
        $token = '';

        foreach ($records as $record) {

            $asin = $record['asin'];
            $country_code = $record['source'];
            $country_code1 = $country_code;
            $seller_id = $record['seller_id'];
            $auth_id = $record['id'];

            $upsert_asin[] = [
                'asin'  => $asin,
                'user_id' => $seller_id,
                'status'   => 1,
            ];

            $asins[] = $asin;

            $aws_token = Aws_credential::where('id', $auth_id)->get()->pluck('auth_code')->toArray();
            $token = $aws_token[0];

            $country_code = strtolower($country_code);
            $catalog_table = 'catalognew' . $country_code . 's';

            $aws_id = NULL;

            if ($count == 9) {

                $queue_data[] = $this->FetchDataFromCatalog($asins, $country_code, $seller_id, $token, $aws_id);
                $count = 0;
                $asins = [];
            }

            $count++;
        }

        if ($asins) {
            $queue_data[] = $this->FetchDataFromCatalog($asins, $country_code, $seller_id, $token, $aws_id);
        }

        $NewCatalogs = [];
        $country_code1 = strtolower($country_code1);

        foreach ($queue_data as $record) {

            if ($record) {

                foreach ($record as $key1 => $value) {

                    foreach ($value as $key => $data) {

                        if ($key != '0') {

                            $key = ($key == "browseClassification") ? "browse_classification" : $key;
                            $key = ($key == "itemClassification") ? "item_classification" : $key;
                            $key = ($key == "modelNumber") ? "model_number" : $key;
                            $key = ($key == "packageQuantity") ? "package_quantity" : $key;
                            $key = ($key == "productTypes") ? "product_types" : $key;
                            $key = ($key == "websiteDisplayGroup") ? "product_types" : $key;
                            $key = ($key == "itemName") ? "item_name" : $key;
                            $key = ($key == "partNumber") ? "part_number" : $key;

                            $NewCatalogs[$key1][$key] = $data;
                        }
                    }

                    $NewCatalogs[$key1]['created_at'] = now();
                    $NewCatalogs[$key1]['updated_at'] = now();
                }
            }
        }

        if (isset($country_code1) && !empty($country_code1)) {

            foreach ($NewCatalogs as $NewCatalog) {

                if (strtolower($country_code1) == "us") {
                    Catalog_us::insert($NewCatalog);
                } else  if (strtolower($country_code1) == "in") {
                    Catalog_in::insert($NewCatalog);
                } else  if (strtolower($country_code1) == "ae") {
                    Catalog_ae::insert($NewCatalog);
                } else  if (strtolower($country_code1) == "sa") {
                    Catalog_sa::insert($NewCatalog);
                }
            }
        }
    }

    public function FetchDataFromCatalog($asins, $country_code, $seller_id, $token, $aws_id)
    {
        $country_code = strtoupper($country_code);
        $config =   $this->config($aws_id, $country_code, $token);
        $apiInstance = new CatalogItemsV20220401Api($config);
        $marketplace_id = $this->marketplace_id($country_code);
        $marketplace_id = [$marketplace_id];

        $identifiers_type = 'ASIN';
        $page_size = 10;
        $locale = null;
        $seller_id_temp = null;
        $keywords = null;
        $brand_names = null;
        $classification_ids = null;
        $page_token = null;
        $keywords_locale = null;

        $incdata = ['attributes', 'dimensions', 'productTypes', 'images', 'summaries'];

        try {
            $result = $apiInstance->searchCatalogItems(
                $marketplace_id,
                $asins,
                $identifiers_type,
                $incdata,
                $locale,
                $seller_id_temp,
                $keywords,
                $brand_names,
                $classification_ids,
                $page_size,
                $page_token,
                $keywords_locale
            );
            $result = (array) json_decode(json_encode($result));

            $queue_data = [];
            $check_asin = [];
            foreach ($result['items'] as $key => $record) {
                $check_asin[] = $record->asin;

                $queue_data[$key]['seller_id'] = $seller_id;
                $queue_data[$key]['source'] = $country_code;
                foreach ($record as $key1 => $value) {

                    if ($key1 == 'summaries') {

                        foreach ($value[0] as $key2 => $value2) {

                            $key2 = str_replace('marketplaceId', 'marketplace', $key2);
                            $queue_data[$key][$key2] = $this->returnDataType($value2);
                        }
                    } else {
                        $queue_data[$key][$key1] = $this->returnDataType($value);
                    }
                    if ($key1 == 'dimensions') {

                        if (array_key_exists('package', (array)$value[0])) {

                            foreach ($value[0]->package as $key3 => $value3) {

                                $queue_data[$key][$key3] = $value3->value;
                                if ($key3 == 'height' || $key3 == 'width' || $key3 == 'length') {

                                    $queue_data[$key]['unit'] = $value3->unit;
                                }
                                if ($key3 == 'weight') {

                                    $queue_data[$key]['weight_unit'] = $value3->unit;
                                }
                            }
                        }
                    }
                }
            }
            $miss_asin_array = [];
            $miss_asin = [];
            $diffs = array_diff($asins, $check_asin);
            foreach ($diffs as $diff) {
                $miss_asin[] = [
                    'asin' => $diff,
                    'user_id' => $seller_id,
                    'source' => $country_code,
                ];
            }
            CatalogMissingAsin::upsert($miss_asin, ['asin'], ['asin', 'source']);

            return $queue_data;
        } catch (Exception $e) {

            $getMessage = $e->getMessage();
            $getCode = $e->getCode();
            $getFile = $e->getFile();

            $slackMessage = "Message: $getMessage
            Code: $getCode
            File: $getFile";

            slack_notification('app360', 'Amazon Catalog Import', $slackMessage);
        }
    }

    public function returnDataType($type)
    {
        $data = '';
        if (is_object($type)) {
            $data = json_encode($type);
        } elseif (is_string($type)) {
            $data = $type;
        } else {
            $data = json_encode($type);
        }
        return $data;
    }
}
