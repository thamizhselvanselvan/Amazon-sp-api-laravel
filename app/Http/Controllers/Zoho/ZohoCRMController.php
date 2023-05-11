<?php

namespace App\Http\Controllers\Zoho;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Zoho\ZohoBulkReadAPI;

class ZohoCRMController extends Controller
{
    public function ZohoWebhook(Request $request)
    {
        $response = $request->all();
        Log::debug($response);
        $zoho_id = $response['job_id'];
        $zoho_state = $request['state'];
        $page = $request['result']['page'];
        $more_records = isset($request['result']['more_records']) && !empty($request['result']['more_records']) ? $request['result']['more_records'] : '0';

        $token_file = "zoho";
        $callback_url = "https://app.360ecom.io/api/zoho/webhook";

        // commandExecFunc("mosh:zoho-bulk-dump ${zoho_id} ${zoho_state} ${page} ${more_records}");
        $zoho_bulk_api = new ZohoBulkReadAPI();
        $zoho_bulk_api->zohoDump($zoho_id, $zoho_state, $page, $more_records, $token_file, $callback_url);
    }

    public function NewZohoWebhook(Request $request)
    {
        $response = $request->all();
        Log::debug($response);
        $zoho_id = $response['job_id'];
        $zoho_state = $request['state'];
        $page = $request['result']['page'];
        $more_records = isset($request['result']['more_records']) && !empty($request['result']['more_records']) ? $request['result']['more_records'] : '0';

        $token_file = "new_zoho";
        $callback_url = "https://app.360ecom.io/api/zoho/new/webhook";

        // commandExecFunc("mosh:zoho-bulk-dump ${zoho_id} ${zoho_state} ${page} ${more_records}");
        $zoho_bulk_api = new ZohoBulkReadAPI();
        $zoho_bulk_api->zohoDump($zoho_id, $zoho_state, $page, $more_records, $token_file, $callback_url);
    }
}
