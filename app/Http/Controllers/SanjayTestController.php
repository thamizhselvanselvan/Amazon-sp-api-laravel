<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aws_credential;
use App\Services\SP_API\API\CategoryTreeReport;

class SanjayTestController extends Controller
{
    public function index()
    {
        $seller_id = '22';
        $aws = Aws_credential::with(['mws_region'])->where('seller_id', $seller_id)->where('api_type', 1)->first();
        $aws_key = $aws->id;
        $country_code = $aws->mws_region->region_code;
        $marketplace_id = $aws->mws_region->marketplace_id;

        $data = new CategoryTreeReport();
        $data->createReport($aws_key, $country_code, $marketplace_id);
    }
}
