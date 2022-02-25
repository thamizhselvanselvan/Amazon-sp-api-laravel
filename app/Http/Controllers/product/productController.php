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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Config\ConfigTrait;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Artisan;
use Maatwebsite\Excel\Concerns\ToArray;
use SellingPartnerApi\Api\CatalogItemsV0Api;


class productController extends Controller
{
   
    public function index(){

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
       
    }
}
