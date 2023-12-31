<?php

namespace App\Jobs;

use Exception;
use App\Models\Mws_region;
use Illuminate\Bus\Queueable;
use App\Models\Inventory\Catalog;
use Illuminate\Support\Facades\Log;
use App\Models\Admin\ErrorReporting;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\SP_API\Config\ConfigTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use SellingPartnerApi\Api\CatalogItemsV0Api;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class InventoryCatalogImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use ConfigTrait;
    private $payload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $datas = $this->payload['data'];

        foreach ($datas as $value) {

            $asin = $value['asin'];
            $country_code = $value['source'];

            $mws_region = Mws_region::with('aws_verified')->where('region_code', $country_code)->get()->first();
            $auth_code = (!is_null($mws_region->aws_verified->first()) ? $mws_region->aws_verified->first()->auth_code : null);
            // $auth_code = ($mws_region['aws_verified']['auth_code']);
            $aws_id = '';
            $config = $this->config($aws_id, $country_code, $auth_code);

            $apiInstance = new CatalogItemsV0Api($config);
            $marketplace = $this->marketplace_id($country_code);

            try {
                $result = $apiInstance->getCatalogItem($marketplace, $asin);
                $result = json_decode(json_encode($result));

                $data_formate = (array)($result->payload->AttributeSets[0]);
                $title = $data_formate['Title'];
                Catalog::where('source', $country_code)->where('asin', $asin)->update(['item_name' => $title]);
            } catch (Exception $e) {
                $code =  $e->getCode();
                $msg = $e->getMessage();
                $error_reportings = ErrorReporting::create([
                    'queue_type' => "Inventory",
                    'identifier' => $asin,
                    'identifier_type' => "ASIN",
                    'source' => $country_code,
                    'aws_key' => $aws_id,
                    'error_code' => $code,
                    'message' => $msg,
                ]);
            }
        }
    }
}
