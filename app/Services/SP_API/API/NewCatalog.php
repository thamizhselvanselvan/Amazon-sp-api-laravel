<?php

namespace App\Services\SP_API\API;

use config;
use Exception;
use RedBeanPHP\R;
use App\Models\Mws_region;
use App\Models\Catalog\AsinSource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

            $mws_region = Mws_region::with(['aws_verified'])->where('region_code', $country_code)->get()->first();
            $token = $mws_region['aws_verified']['auth_code'];
            $country_code = strtolower($country_code);
            $catalog_table = 'catalognew' . $country_code . 's';

            $aws_id = NULL;
            $catalog_details = $this->FetchDataFromCatalog($asin, $country_code, $seller_id, $token, $aws_id);

            if ($catalog_details) {

                $found = DB::connection('catalog')->select("SELECT id, asin FROM $catalog_table 
                WHERE asin = '$asin' ");

                if (count($found) == 0) {
                    //new details

                    $queue_data[] = $catalog_details;
                } else {
                    //update
                    Log::info('asin details updating -> ' . $asin);

                    $asin_id = $found[0]->id;
                    $asin_details = R::load($catalog_table, $asin_id);
                    foreach ($catalog_details as $key => $key_value) {

                        $asin_details->$key = $key_value;
                    }
                    $asin_details->updated_at = now();
                    R::store($asin_details);
                }
            }
        }

        $NewCatalogs = [];
        $country_code1 = strtolower($country_code1);
        $catalog_table = 'catalognew' . $country_code1 . 's';
        foreach ($queue_data as $key1 => $value) {
            $NewCatalogs[] = R::dispense($catalog_table);
            foreach ($value as $key => $data) {
                $NewCatalogs[$key1]->$key = $data;
            }
            $NewCatalogs[$key1]->created_at = now();
            $NewCatalogs[$key1]->updated_at = now();
        }
        R::storeALL($NewCatalogs);
    }

    public function FetchDataFromCatalog($asin, $country_code, $seller_id, $token, $aws_id)
    {
        $country_code = strtoupper($country_code);
        $config =   $this->config($aws_id, $country_code, $token);
        $apiInstance = new CatalogItemsV20220401Api($config);
        $marketplace_id = $this->marketplace_id($country_code);
        $incdata = ['attributes', 'dimensions', 'productTypes', 'images', 'summaries'];

        try {
            $result = $apiInstance->getCatalogItem($asin, $marketplace_id, $incdata);
            $result = json_decode(json_encode($result));

            $queue_data = [];
            $queue_data['seller_id'] = $seller_id;
            $queue_data['source'] = $country_code;
            foreach ($result as $key => $value) {

                if ($key == 'summaries') {
                    foreach ((array)$value[0] as $key2 => $value2) {
                        $key2 = str_replace('marketplaceId', 'marketplace', $key2);
                        $queue_data[$key2] = $this->returnDataType($value2);
                    }
                } else {
                    $queue_data[$key] = $this->returnDataType($value);
                }
            }
            return $queue_data;
        } catch (Exception $e) {

            $country_code = strtolower($country_code);
            $catalog_table = 'catalognew' . $country_code . 's';

            $found = DB::connection('catalog')->select("SELECT id, asin FROM $catalog_table 
            WHERE asin = '$asin' ");

            if (count($found) == 0) {

                $NewCatalogs = R::dispense($catalog_table);
                $NewCatalogs->asin = $asin;
                R::store($NewCatalogs);
            }
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
