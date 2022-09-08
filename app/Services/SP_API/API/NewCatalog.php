<?php

namespace App\Services\SP_API\API;

use config;
use Exception;
use RedBeanPHP\R;
use App\Models\Mws_region;
use App\Models\Catalog\AsinSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Admin\ErrorReporting;
use App\Services\SP_API\Config\ConfigTrait;
use SellingPartnerApi\Api\CatalogItemsV20220401Api;

class NewCatalog
{
    use ConfigTrait;

    public function Catalog($records, $seller_id = NULL)
    {
        $this->RedBeanConnection();
        $queue_data = [];
        $upsert_asin = [];
        $country_code1 = '';
        $asins = [];
        $count = 0;

        foreach ($records as $record) {
            $asin = $record['asin'];
            $country_code = $record['source'];
            $country_code1 = $country_code;
            $seller_id = $record['seller_id'];

            $upsert_asin[] = [
                'asin'  => $asin,
                'user_id' => $seller_id,
                'status'   => 1,
            ];
            $asins[] = $asin;

            $mws_region = Mws_region::with(['aws_verified'])->where('region_code', $country_code)->get()->first();
            $token = $mws_region['aws_verified']['auth_code'];
            $country_code = strtolower($country_code);
            $catalog_table = 'catalognew' . $country_code . 's';

            $aws_id = NULL;
            if ($count == 19) {

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
        $catalog_table = 'catalognew' . $country_code1 . 's';
        foreach ($queue_data as $record) {
            if ($record) {
                foreach ($record as $key1 => $value) {
                    $NewCatalogs[] = R::dispense($catalog_table);
                    foreach ($value as $key => $data) {

                        if ($key != '0') {

                            $NewCatalogs[$key1]->$key = $data;
                        }
                    }
                    $NewCatalogs[$key1]->created_at = now();
                    $NewCatalogs[$key1]->updated_at = now();
                }
            }
        }
        R::storeALL($NewCatalogs);
    }


    public function FetchDataFromCatalog($asins, $country_code, $seller_id, $token, $aws_id)
    {
        $country_code = strtoupper($country_code);
        $config =   $this->config($aws_id, $country_code, $token);
        $apiInstance = new CatalogItemsV20220401Api($config);
        $marketplace_id = $this->marketplace_id($country_code);
        $marketplace_id = [$marketplace_id];

        $identifiers_type = 'ASIN';
        $page_size = 20;
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

            foreach ($result['items'] as $key => $record) {
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
            return $queue_data;
        } catch (Exception $e) {

            log::alert($e);
            // $country_code = strtolower($country_code);
            // $catalog_table = 'catalognew' . $country_code . 's';
            // $error_record = [
            //     'queue_type' => 'Catalog',
            //     'source' => $country_code,
            //     'identifier' => $e,
            //     'identifier_type' => 'ASIN',
            // ];

            // ErrorReporting::insert($error_record);
        }
    }

    public function RedBeanConnection()
    {
        $host = config('database.connections.catalog.host');
        $dbname = config('database.connections.catalog.database');
        $port = config('database.connections.catalog.port');
        $username = config('database.connections.catalog.username');
        $password = config('database.connections.catalog.password');

        if (!R::testConnection('catalog', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password)) {
            R::addDatabase('catalog', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
            R::selectDatabase('catalog');
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
