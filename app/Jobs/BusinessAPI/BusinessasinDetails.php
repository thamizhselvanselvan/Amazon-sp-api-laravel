<?php

namespace App\Jobs\BusinessAPI;

use RedBeanPHP\R;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Services\AWS_Business_API\AWS_POC\ProductsRequest;

class BusinessasinDetails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $host = config('database.connections.business.host');
        $dbname = config('database.connections.business.database');
        $port = config('database.connections.business.port');
        $username = config('database.connections.business.username');
        $password = config('database.connections.business.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
        $start_time = startTime();
        $end_time = endTime($start_time);
        $rec =   $this->payload['data'];

        foreach ($rec as $val) {
            $fetched[] = ($val->asin);
        }
    
        $ApiCall = new ProductsRequest();
        $counter = 1;
        foreach ($fetched as $data) {
            $asin = $data;

            $data = $ApiCall->getASINpr($asin);
             Log::notice([$data]);

            if (property_exists($data, "errors") && $data->errors[0]->code == "PRODUCT_NOT_FOUND") {

                $asin = 'Not Found';
                $asin_type = 'Not Found';
                $signedProductId  = 'Not Found';
                $offers = 'Not Found';
                $availability = 'Not Found';
                $buyingGuidance = 'Not Found';
                $fulfillmentType = 'Not Found';
                $merchant = 'Not Found';
                $offerId = 'Not Found';
                $price = 'Not Found';
                $listPrice = 'Not Found';
                $productCondition = 'Not Found';
                $condition = 'Not Found';
                $quantityLimits = 'Not Found';
                $deliveryInformation = 'Not Found';
                $features = 'Not Found';
                $taxonomies = 'Not Found';
                $title = 'Not Found';
                $url = 'Not Found';
                $productOverview = 'Not Found';
                $productVariations = 'Not Found';
            } else {

                $asin = ($data->asin);
              
                $asin_type = ($data->asinType);
                $signedProductId  = ($data->signedProductId);
                if ($data->includedDataTypes->OFFERS == []) {
                    $offers = 'null';
                    $availability = 'null';
                    $buyingGuidance = 'null';
                    $fulfillmentType = 'null';
                    $merchant = 'null';
                    $offerId = 'null';
                    $price = 'null';
                    $listPrice = 'null';
                    $productCondition = 'null';
                    $condition = 'null';
                    $quantityLimits = 'null';
                    $deliveryInformation = 'null';
                } else   if (property_exists($data, "errors") && $data->errors[0]->code == "You exceeded your quota for the requested resource.") {
                    $end_time = endTime($start_time);
                    Log::info("After this $counter much request 429 error came. timing $end_time");
                } else {
                    $offers = json_decode(json_encode($data->includedDataTypes->OFFERS[0]));
                    $availability = ($offers->availability);
                    $buyingGuidance = ($offers->buyingGuidance);
                    $fulfillmentType = ($offers->fulfillmentType);
                    $merchant = json_encode($offers->merchant);
                    $offerId = ($offers->offerId);
                    $price = json_encode($offers->price);
                    $listPrice = json_encode($offers->listPrice);
                    $productCondition = ($offers->productCondition);
                    $condition = json_encode($offers->condition);
                    $quantityLimits = json_encode($offers->quantityLimits);
                    $deliveryInformation = ($offers->deliveryInformation);
                }
                $features = json_encode($data->features);
                $taxonomies = json_encode($data->taxonomies);
                $title = ($data->title);
                $url = ($data->url);
                $productOverview = json_encode($data->productOverview);
                $productVariations = json_encode($data->productVariations);
            }
            $end_time = endTime($start_time);
            // Log::alert("Before Update Query - $end_time");
            // DB::connection('mongodb')->table('product_details')->where('asin', $asin)->update(
            //     [
            //         'asin' => $asin,
            //         'asin_type' => $asin_type,
            //         'signedProductId ' => $signedProductId,
            //         'offers' => $offers,
            //         'availability' => $availability,
            //         'buyingGuidance' => $buyingGuidance,
            //         'fulfillmentType' => $fulfillmentType,
            //         'merchant' => $merchant,
            //         'offerId' => $offerId,
            //         'price' => $price,
            //         'listPrice' => $listPrice,
            //         'productCondition' => $productCondition,
            //         'condition' => $condition,
            //         'quantityLimits' => $quantityLimits,
            //         'deliveryInformation' => $deliveryInformation,
            //         'features' => $features,
            //         'taxonomies' => $taxonomies,
            //         'title' => $title,
            //         'url' => $url,
            //         'productOverview' => $productOverview,
            //         'productVariations' => $productVariations,
            //         'created_at' => now()->format('Y-m-d H:i:s'),
            //         'updated_at'  => now()->format('Y-m-d H:i:s')
            //     ],
            //     ["upsert" => true]
            // );

            $data = R::dispense('uscatalog');
            $data->asin = $asin;
            $data->asin_type = $asin_type;
            $data->signedProductid_ =  $signedProductId;
            $data->availability = $availability;
            $data->buyingGuidance = $buyingGuidance;
            $data->fulfillmentType =  $fulfillmentType;
            $data->merchant   =  $merchant;
            $data->offerid_ =  $offerId;
            $data->price =   $price;
            $data->listPrice = $listPrice;
            $data->productCondition = $productCondition;
            $data->condition =   $condition;
            $data->quantityLimits =  $quantityLimits;
            $data->deliveryInformation =  $deliveryInformation;
            $data->features =     $features;
            $data->taxonomies = $taxonomies;
            $data->title = $title;
            $data->url = $url;
            $data->productOverview =  $productOverview;
            $data->productOverview =  $productVariations;

            R::store($data);

            $end_time = endTime($start_time);
            //  Log::alert("After Update Query - $end_time");
            $counter++;
        }
        $finished_loop = endTime($start_time);
        // Log::alert("FInal Query Time $finished_loop");
    }
}
