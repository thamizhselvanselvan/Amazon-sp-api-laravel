<?php

namespace App\Http\Controllers\Orders;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\order\OrderSellerCredentials;

class OrdersDashboardController extends Controller
{
    public function Dashboard()
    {

        $order_sql = DB::connection('order')->select('select order_status, our_seller_identifier,COUNT(order_status) as count, os.store_name, os.country_code from orders join ord_order_seller_credentials as os where os.seller_id = orders.our_seller_identifier GROUP BY our_seller_identifier,order_status');

        $latest_update = DB::connection('order')->select('select createdat, updatedat, our_seller_identifier, os.store_name from orders join ord_order_seller_credentials as os where os.seller_id = orders.our_seller_identifier group by createdat, updatedat, our_seller_identifier order by updatedat DESC');

        $order_collect = collect($order_sql);
        $order_groupby = $order_collect->groupBy('store_name');

        $latest_update_collect = collect($latest_update);
        $latest_update_collect = $latest_update_collect->groupBy('store_name');

        $store_latest = [];
        foreach ($latest_update_collect as $key => $value) {
            $updateat = $value[0]->updatedat;
            if ($updateat != '') {
                $store_latest[$key] = $updateat;
            } else {
                $store_latest[$key] = $value[0]->createdat;
            }
        }

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
                        $order_status[$data->order_status] = $data->count;
                        $total += $data->count;
                        $country_name = $data->country_code;
                    }
                }
            }
            $country = $key . ' [' . $country_name . ']';
            $order_status['Total'] = $total;
            $time = str_replace(' ','.',$store_latest[$key]);
            $date = $this->CarbonGetDateDiff($time);
            $order_status['last_updated'] = $date;
            $order_status_count[$country] = $order_status;
        }

        // dd($order_status_count);
        return view('orders.dashboard', compact(['order_status_count']));
    }

    public function CarbonGetDateDiff($date)
    {
        $date_details_array = ['Year', 'Month', 'Day', 'Hour', 'Minute', 'Second'];

        $date = substr($date, 0, strpos($date, "."));
        $created = new Carbon($date);
        $now = Carbon::now();
        $differnce = $created->diff($now);
        $final_date = '';
        $count = 0;
        foreach ((array)$differnce as $key => $value) {
            if ($value != 0 && $count < 6 && $count > 2) {
                $final_date .= $value > 1 ? $value . ' ' . $date_details_array[$count] . 's, ' : $value . ' ' . $date_details_array[$count] . ',  ';
            }
            $count++;
        }
        $time = rtrim($final_date, ' ,') . ' Before';
        $date =  $differnce->days > 1 ? $differnce->days . ' Days' : 'Today';
        return $date. ', '.$time;
    }
}
