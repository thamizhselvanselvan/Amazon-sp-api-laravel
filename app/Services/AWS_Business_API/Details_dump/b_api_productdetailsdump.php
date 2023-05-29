<?php

namespace App\Services\AWS_Business_API\Details_dump;


use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Support\BusinessAPI\ProductSearch;


class b_api_productdetailsdump
{

    public function savedetails($asin)
    {
        $start_time = startTime();
        $end_time = endTime($start_time);
        $rec =  $asin;
        foreach ($rec as $val) {
            $fetched[] = ($val->asin);
        }
        $counter = 1;
        foreach ($fetched as $asin) {
            try {

                $data = (new ProductSearch())->search_1($asin);

                if (isset($data->products[0]->asin)) {
                    $asin = $data->products[0]->asin;
                    $asinType = $data->products[0]->asinType;
                    $signedProductId = $data->products[0]->signedProductId;
                    $offers = json_encode($data->products[0]->includedDataTypes->OFFERS);
                    $images = json_encode($data->products[0]->includedDataTypes->IMAGES);
                    $features = json_encode($data->products[0]->features);
                    $taxonomies = json_encode($data->products[0]->taxonomies);
                    $title = ($data->products[0]->title);
                    $url = ($data->products[0]->url);
                    $bookInformation = json_encode($data->products[0]->bookInformation);
                    $mediaInformation = json_encode($data->products[0]->mediaInformation);
                    $productOverview = json_encode($data->products[0]->productOverview);
                    $productDetails = json_encode($data->products[0]->productDetails);

                    $product_varient_dimensions = json_encode($data->products[0]->productVariations->dimensions);
                    $product_variations = json_encode($data->products[0]->productVariations->variations);
                    // $productDescription = json_encode($data->products[0]->productDescription);

                    DB::connection('mongodb')->table('business_api_catalog_details')->where('asin', $asin)->update(
                        [
                            'asin' =>      $asin,
                            'title' =>    $title,
                            'url' =>    $url,
                            'images' => $images,
                            'asin_type' => $asinType,
                            // 'product_description' => $productDescription,
                            'signed_productid' => $signedProductId,
                            'offers' => $offers,
                            'features' => $features,
                            'taxonomies' => $taxonomies,
                            'book_information' =>    $bookInformation,
                            'media_information' =>    $mediaInformation,
                            'product_overview' =>   $productOverview,
                            'product_details' =>   $productDetails,
                            'product_varient_dimensions' =>   $product_varient_dimensions,
                            'product_variations' =>   $product_variations,
                            'created_at' => now()->format('Y-m-d H:i:s'),
                            'updated_at'  => now()->format('Y-m-d H:i:s')
                        ],
                        ["upsert" => true]
                    );
                } else {
                    Log::alert('no data');
                }
            } catch (Exception $e) {
                Log::Alert(json_encode($data));
                Log::Alert($asin);
                Log::notice($e);
            }
        }
    }
}
