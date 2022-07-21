<?php

namespace App\Jobs;

use Exception;
use App\Models\Mws_region;
use Illuminate\Bus\Queueable;
use App\Models\Inventory\Catalog;
use Illuminate\Support\Facades\Log;
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
        // Log::alert('Inv Catalog working');
        foreach ($datas as $value) {

            $asin = $value['asin'];
            $country_code = $value['source'];

            $mws_region = Mws_region::with('aws_verified')->where('region_code', $country_code)->get()->first();

            $auth_code = ($mws_region['aws_verified']['auth_code']);
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
                Log::info("Inv Job compleated");
                Log::alert($data_formate['Title']);
            } catch (Exception $e) {
                Log::warning($e);
            }
        }
    }
}
