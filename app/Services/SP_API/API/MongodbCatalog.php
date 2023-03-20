<?php

namespace App\Services\SP_API\API;

use Exception;
use App\Models\Aws_credential;
use App\Services\Config\ConfigTrait;
use App\Models\Catalog\MongoCatalogae;
use App\Models\Catalog\MongoCatalogin;
use App\Models\Catalog\MongoCatalogus;
use SellingPartnerApi\Api\CatalogItemsV20220401Api;

class MongodbCatalog
{
    use ConfigTrait;

    public function index($records, $seller_id = NULL)
    {
        $queue_data = [];
        $aws_id = NULL;
        $country_code = '';
        $seller_id = '';
        $auth_id = '';
        $token = '';
        if (isset($records[0])) {

            $country_code = strtoupper($records[0]['source']);
            $seller_id = $records[0]['seller_id'];
            $auth_id = $records[0]['id'];
            $aws_token = Aws_credential::where('id', $auth_id)->get()->pluck('auth_code')->toArray();
            $token = $aws_token[0];
        }

        foreach ($records as $record) {

            $asin[] = $record['asin'];
        }
        if (count($asin) != 0) {

            $queue_data[] = $this->FetchDataFromCatalog($asin, $country_code, $seller_id, $token, $aws_id);
        }

        if (isset($queue_data[0])) {
            foreach ($queue_data[0] as $data) {

                if ($country_code == 'IN') {
                    MongoCatalogin::where('asin', $data['asin'])->update($data, ['upsert' => true]);
                } elseif ($country_code == 'US') {
                    MongoCatalogus::where('asin', $data['asin'])->update($data, ['upsert' => true]);
                } elseif ($country_code == 'AE') {
                    MongoCatalogae::where('asin', $data['asin'])->update($data, ['upsert' => true]);
                }
            }
        }
    }

    public function FetchDataFromCatalog($asins, $country_code, $seller_id, $token, $aws_id)
    {
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

        $incdata = ['attributes', 'dimensions', 'identifiers', 'relationships', 'salesRanks', 'productTypes', 'images', 'summaries'];

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

                    $queue_data[$key][$key1] = $this->getDataType($value);
                }
            }
            return $queue_data;
        } catch (Exception $e) {
        }
    }

    public function getDataType($type)
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
