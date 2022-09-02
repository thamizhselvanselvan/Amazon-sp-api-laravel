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
        $startTime = Carbon::now();
        $endTime = Carbon::now()->subDays(30);

        $order_sql = DB::connection('order')->select("SELECT order_status, our_seller_identifier,COUNT(order_status) as count, os.store_name, os.country_code from orders 
        join ord_order_seller_credentials as os 
        where os.seller_id = orders.our_seller_identifier 
        AND orders.updatedat BETWEEN '$endTime' AND '$startTime' 
        GROUP BY our_seller_identifier, order_status");

        // dd($order_sql);

        $latest_update = DB::connection('order')->select("SELECT updatedat, our_seller_identifier, os.store_name FROM orders 
        join ord_order_seller_credentials as os where os.seller_id = orders.our_seller_identifier 
        group by updatedat, our_seller_identifier 
        order by updatedat DESC");
        // dd($latest_update);
        $order_collect = collect($order_sql);
        $order_groupby = $order_collect->groupBy('store_name');

        $latest_update_collect = collect($latest_update);
        $latest_update_collect = $latest_update_collect->groupBy('store_name');

        $store_latest = [];
        foreach ($latest_update_collect as $key => $value) {

            $updatedat = $value[0]->updatedat;
            $store_latest[$key] = $updatedat;
        }

        $order_status_count = [];
        foreach ($order_groupby as $key => $value) {
            $order_status = [
                'LastUpdate' => 0,
                'PartiallyShipped' => 0,
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
            $time = $store_latest[$key];
            $date = $this->CarbonGetDateDiff($time . '.000');
            $order_status['LastUpdate'] = $date;
            // $time = str_replace(' ','.',$store_latest[$key]);
            $order_status_count[$country] = $order_status;
        }
        // dd($order_status_count);
        return view('orders.dashboard', compact(['order_status_count']));
    }

    public function OrderItemDashboard()
    {
        $today = Carbon::now();
        $month = Carbon::now()->subMonth();

        $latest = DB::connection('order')->select("SELECT seller_identifier, max(od.updated_at) as latest, orsc.store_name, orsc.country_code 
        FROM orderitemdetails as od
        JOIN ord_order_seller_credentials as orsc
        where od.seller_identifier = orsc.seller_id
        GROUP BY seller_identifier
        ");

        foreach ($latest as $key => $value) {
            foreach ($latest as $date) {
                $store_name = $date->store_name;
                $store_time = $date->latest != '' ? $date->latest : $month;
                $country_name = $date->country_code;
                $age[$store_name . ' [' . $country_name . ']'] = $this->CarbonGetDateDiff($store_time . '.000');
            }
        }

        return view('orders.orderItemDashboard', compact('age'));
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
        return $date . ', ' . $time;
    }

    public function AwsOrderDashboard()
    {

        $endTime = Carbon::now();
        $startTime = Carbon::now()->subDays(5);

        $nitrous_zoho = DB::connection('aws')
            ->select("SELECT amazon_order_id, purchase_date
            FROM nitrous_amazon_order_details
            where zoho_order_id = ''
            AND purchase_date BETWEEN '$startTime' AND '$endTime'  
            ORDER BY purchase_date DESC");

        $ckshop = DB::connection('aws')
            ->select("SELECT amazon_order_id, purchase_date
            FROM ckshop_amazon_order_details
            where zoho_order_id = ''
            AND purchase_date BETWEEN '$startTime' AND '$endTime'  
            ORDER BY purchase_date DESC");

        $gotech = DB::connection('aws')
            ->select("SELECT amazon_order_id, purchase_date
            FROM gotech_amazon_order_details
            where zoho_order_id = ''
            AND purchase_date BETWEEN '$startTime' AND '$endTime'  
            ORDER BY purchase_date DESC");

        $gotech_uae = DB::connection('aws')
            ->select("SELECT amazon_order_id, purchase_date
            FROM gotech_uae_amazon_order_details
            where zoho_order_id = ''
            AND purchase_date BETWEEN '$startTime' AND '$endTime'  
            ORDER BY purchase_date DESC");

        $in_mbm = DB::connection('aws')
            ->select("SELECT amazon_order_id, purchase_date
            FROM in_mbm_amazon_order_details
            where zoho_order_id = ''
            AND purchase_date BETWEEN '$startTime' AND '$endTime'  
            ORDER BY purchase_date DESC");

        $mahzuz_uae = DB::connection('aws')
            ->select("SELECT amazon_order_id, purchase_date
            FROM mahzuz_uae_amazon_order_details
            where zoho_order_id = ''
            AND purchase_date BETWEEN '$startTime' AND '$endTime'  
            ORDER BY purchase_date DESC");

        $mbm = DB::connection('aws')
            ->select("SELECT amazon_order_id, purchase_date
            FROM mbm_amazon_order_details
            where zoho_order_id = ''
            AND purchase_date BETWEEN '$startTime' AND '$endTime'  
            ORDER BY purchase_date DESC");

        $mbm_saudi = DB::connection('aws')
            ->select("SELECT amazon_order_id, purchase_date
            FROM mbm_saudi_amazon_order_details
            where zoho_order_id = ''
            AND purchase_date BETWEEN '$startTime' AND '$endTime'  
            ORDER BY purchase_date DESC");

        $nitshopp = DB::connection('aws')
            ->select("SELECT amazon_order_id, purchase_date
            FROM nitshopp_in_amazon_order_details
            where zoho_order_id = ''
            AND purchase_date BETWEEN '$startTime' AND '$endTime'  
            ORDER BY purchase_date DESC");

        $pram = DB::connection('aws')
            ->select("SELECT amazon_order_id, purchase_date
            FROM pram_amazon_order_details
            where zoho_order_id = ''
            AND purchase_date BETWEEN '$startTime' AND '$endTime'  
            ORDER BY purchase_date DESC");

        $pram_uae = DB::connection('aws')
            ->select("SELECT amazon_order_id, purchase_date
            FROM pram_uae_amazon_order_details
            where zoho_order_id = ''
            AND purchase_date BETWEEN '$startTime' AND '$endTime'  
            ORDER BY purchase_date DESC");

        $all_store_details = [
            'Nitrous' => $nitrous_zoho,
            'CliQKart' => $ckshop,
            'Gotech' => $gotech,
            'Gotech_UAE' => $gotech_uae,
            'MBM_IN' => $in_mbm,
            'MBM' => $mbm,
            'Mahzuz_UAE' => $mahzuz_uae,
            'MBM_Saudi' => $mbm_saudi,
            'Nitshopp' => $nitshopp,
            'Pram' => $pram,
            'Pram_UAE' => $pram_uae
        ];

        // dd($all_store_details);
        return view('orders.AwsOrderDashboard', compact('all_store_details'));
    }
}
