<?php

namespace App\Http\Controllers\product;

use Exception;
use League\Csv\Reader;
use RedBeanPHP\R as R;
use League\Csv\Statement;
use App\Models\asinMaster;
use Illuminate\Http\Request;
use League\Csv\XMLConverter;
use App\Models\aws_credentials;
use SellingPartnerApi\Endpoint;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Config\ConfigTrait;
use Illuminate\Support\Arr;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Concerns\ToArray;
use SellingPartnerApi\Api\CatalogItemsV0Api;
use SellingPartnerApi\Api\ProductPricingApi;


class productController extends Controller
{
    use ConfigTrait;
    public function index(Request $request){

        $data = DB::select('select asin,source,label,item_dimensions,currency_code,amount from amazon');
        
        $data = (json_decode($data[0]->item_dimensions));
        
        // dd($data);
        //dd($data->Weight->value);
        if($request->ajax()){
            $data = DB::select('select asin,source,label,item_dimensions,currency_code,amount from amazon');
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('item_dimensions',function ($row){
                    $data = json_decode($row->item_dimensions);
                    $dimension = '<p class="m-0 p-0">Height: '. $data->Height->value .' '. $data->Height->Units . '</p>';
                    $dimension .= '<p class="m-0 p-0">Length: '. $data->Length->value .' '. $data->Length->Units . '</p>';
                    $dimension .= '<p class="m-0 p-0">Width: '. $data->Width->value .' '. $data->Width->Units . '</p>';

                    return $dimension;
                })
                ->editColumn('amount',function ($row){
                    return $row->amount .' ['.$row->currency_code.']' ;
                })
                ->addColumn('weight', function($row){
                    $data = json_decode($row->item_dimensions);
                    $dimension = '<p class="m-0 p-0">Weight: '. $data->Weight->value .' '. $data->Weight->Units . '</p>';
                   return $dimension;

                })
                ->rawColumns(['amount', 'item_dimensions', 'weight'])
                ->make(true);
                
        }
        return view('product.index');
    }

public function fetchFromAmazon(){

    if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

        Log::warning("asin production executed");

        $base_path = base_path();
        $command = "cd $base_path && php artisan pms:catalog-import > /dev/null &";
        exec($command);
        Log::warning("asin production command executed");
        
    } else {

        Log::warning("Export command executed local !");
        Artisan::call('pms:catalog-import');
        
    }
       
    return redirect()->intended('/product/amazon_com');
    }

    public function amazonGetPricing(){

        $startTime = startTime();
        $connection = config('app.connection');
        $host = config('app.host');
        $dbname = config('app.database');
        $port = config('app.port');
        $username = config('app.username');
        $password = config('app.password');

    
        $datas = asinMaster::with(['aws'])->limit(1)->get();

       
            R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

            foreach ($datas as $data) {

                $asin = $data['asin'];

                $country_code = $data['source'];
                $auth_code = $data['aws']['auth_code'];
                $aws_key = $data['aws']['id'];
                // $marketplace_id = $this->marketplace_id($country_code);

                $config = new Configuration([
                    "lwaClientId" => "amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf",
                    "lwaClientSecret" => "5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765",
                    "lwaRefreshToken" => "Atzr|IwEBIP77B_-dQXlLL8PKT9V5aGDCndJl7Jab6V-7ZQMtbysqCDE8fBF-kHzjsiIigTLM1ZBy0XMcqPoqFzBuCEEKXHX3lrKFCFmR4ss4CpTAl-iJ7Z9K_oUHhM1QTu45eov5dQMham40Iqyf0dOWPb4EvRVtaab9uvU4ViPVEvpDRcOskWWY3UC6mv4OSV70et2vE0tqxaTspf1nhWtjveZ_27zAHuBPRM_mU79v1gwphDmZhO5-jgBzB2jtyIe_w0hZkj2Bc-OiaUg4s9c0t7mEAGk_EjPIIWfr0fJ2bixLGCI2WGT1B9-1BN55f4RqTktbztc",
                    "awsAccessKeyId" => "AKIAZTIHMXYBD5SRG5IZ",
                    "awsSecretAccessKey" => "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",
                    "endpoint" => Endpoint::EU,  // or another endpoint from lib/Endpoints.php
                    "roleArn" => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role'
                ]);
                
                $apiInstance = new ProductPricingApi($config);
                $marketplace_id = 'A21TJRUUN4KGV'; // string | A marketplace identifier. Specifies the marketplace for which prices are returned.
                $item_type = 'ASINS'; // string | Indicates whether ASIN values or seller SKU values are used to identify items. If you specify Asin, the information in the response will be dependent on the list of Asins you provide in the Asins parameter. If you specify Sku, the information in the response will be dependent on the list of Skus you provide in the Skus parameter.
                $asins = array($asin); // string[] | A list of up to twenty Amazon Standard Identification Number (ASIN) values used to identify items in the given marketplace.
                $skus = array(); // string[] | A list of up to twenty seller SKU values used to identify items in the given marketplace.
                $item_condition = 'New'; // string | Filters the offer listings based on item condition. Possible values: New, Used, Collectible, Refurbished, Club.
                $offer_type = 'B2C'; // string | Indicates whether to request pricing information for the seller's B2C or B2B offers. Default is B2C.
                

                print_r($asins);

                try {
                    $result = $apiInstance->getPricing($marketplace_id, $item_type, $asins);
                    print_r($result);
                } catch (Exception $e) {
                    echo 'Exception when calling ProductPricingApi->getPricing: ', $e->getMessage(), PHP_EOL;
                }
            }
        } 

    }

