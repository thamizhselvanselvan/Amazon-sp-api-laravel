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
        foreach ($records as $record) {
            $asin = $record['asin'];
            $country_code = $record['source'];
            $seller_id = $record['seller_id'];

            $mws_region = Mws_region::with(['aws_verified'])->where('region_code', $country_code)->get()->first();
            $token = $mws_region['aws_verified']['auth_code'];
            $country_code = strtolower($country_code);
            $catalog_table = 'catalog' . $country_code . 's';
            $tables = [];
            $count = 1;
            // log::alert($country_code);
            $found = DB::connection('catalog')->select("SELECT asin FROM $catalog_table WHERE asin = '$asin' ");
            if (count($found) == 0) {
                $aws_id = NULL;
                $this->FetchDataFromCatalog($asin, $country_code, $seller_id, $token, $aws_id);
            }
        }
    }

    public function FetchDataFromCatalog($asin, $country_code, $seller_id, $token, $aws_id)
    {

        $country_code = strtoupper($country_code);
        $config =   $this->config($aws_id, $country_code, $token);
        $apiInstance = new CatalogItemsV20220401Api($config);
        $marketplace_id = $this->marketplace_id($country_code);
        // $incdata= ['attributes','dimensions', 'identifiers', 'images', 'productTypes', 'relationships', 'salesRanks', 'summaries'];
        $incdata = ['attributes', 'dimensions', 'productTypes', 'images', 'summaries'];
        $country_code = strtolower($country_code);
        $catalog_table = 'catalognew' . $country_code . 's';
        try {
            $result = $apiInstance->getCatalogItem($asin, $marketplace_id, $incdata);
            $result = json_decode(json_encode($result));
            $NewCatalogs = R::dispense($catalog_table);
            $NewCatalogs->seller_id = $seller_id;
            $NewCatalogs->source = $country_code;
            foreach ($result as $key => $value) {
                if ($key == 'summaries') {
                    foreach ((array)$value[0] as $key2 => $value2) {
                        $key2 = str_replace('marketplaceId', 'marketplace', $key2);
                        $NewCatalogs->$key2 = $this->returnDataType($value2);
                    }
                } else {
                    $NewCatalogs->$key = $this->returnDataType($value);
                }
            }
            R::store($NewCatalogs);
            $table_name = table_model_create(country_code:$country_code, model:'Asin_source', table_name:'asin_source_');
            $table_name->upsert([
                'asin' => $asin,
                'user_id' => $seller_id,
                'status' => 1,
            ],['user_asin_unique'], ['asin', 'status']);

        } catch (Exception $e) {
            Log::critical($e);
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
