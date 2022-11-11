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

    public function Order(Request $request)
    {
        if (!isset($request->order_id)) {
            return "Enter Order ID";
        }

        $zoho_order = new ZohoOrder;
        return $zoho_order->index($request->order_id);
    }
}
