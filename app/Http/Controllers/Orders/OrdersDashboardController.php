<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\order\OrderSellerCredentials;

class OrdersDashboardController extends Controller
{
    public function Dashboard()
    {
       
        $order_sql = DB::connection('order')->select('select order_status, our_seller_identifier,COUNT(order_status) as count, os.store_name, os.country_code from orders join ord_order_seller_credentials as os where os.seller_id = orders.our_seller_identifier GROUP BY our_seller_identifier,order_status;');

        $order_collect = collect($order_sql);
        $order_groupby = $order_collect->groupBy('store_name');
        // dd($order_groupby);
        $order_status_count = [];
        foreach ($order_groupby as $key => $value) {
            $order_status = [
                'Unshipped' => 0,
                'Pending' => 0,
                'Canceled' => 0,
                'Shipped' => 0,
                'Total' => 0
            ];
            $total = 0;
            foreach ((array)$value as $value1) {

                foreach ((array)$value1 as $key1 => $data) {
                    if ($data) {
                        // $order_status_count[$key][$data->order_status] = $data->count;
                        $order_status[$data->order_status] = $data->count;
                        $total += $data->count;
                    }
                }
            }
            $order_status['Total'] = $total;
            $order_status_count[$key] = $order_status;
        }

        // dd($order_status_count);
        return view('orders.dashboard', compact(['order_status_count']));
    }
}
