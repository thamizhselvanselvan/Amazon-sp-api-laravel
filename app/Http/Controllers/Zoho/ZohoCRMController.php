<?php

namespace App\Http\Controllers\Zoho;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ZohoCRMController extends Controller
{
    public function ZohoWebhook(Request $request)
    {
        $response = $request->all();
        $zoho_id = $response['job_id'];
        $zoho_state = $request['state'];
        $page = $request['result']['page'];
        $more_records = $request['result']['more_records'];

        commandExecFunc("mosh:zoho-bulk-dump ${zoho_id} ${zoho_state} ${page} ${more_records}");
    }
}
