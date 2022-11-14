<?php

namespace App\Http\Controllers\Zoho;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Zoho\ZohoOrder;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class ZohoController extends Controller
{

    public function save(Request $request)
    {
        if (!isset($request->amazon_order_id)) {
            return response()->json(["note_3" => "Enter Order ID"]);
        }

        $zoho_order = new ZohoOrder;
        $response = $zoho_order->index($request->amazon_order_id);

        return response()->json($response);
    }

    public function form()
    {

        return view('zoho/form');
    }

    public function preview(Request $request)
    {
        $zoho_order = new ZohoOrder;
        $response = $zoho_order->lead_preview($request->amazon_order_id);

        return response()->json($response);
    }
}
