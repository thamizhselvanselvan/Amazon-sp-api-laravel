<?php

namespace App\Services\Inventory;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Exists;

class ReportWeekly
{

    private $connection = '';

    public function __construct()
    {
        $this->connection = DB::connection('inventory');
    }
    /* weekly opeaning count */
    public function OpeningStock(): array
    {

        $items = $this->connection->table('stocks')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
        $closing = [];

        foreach ($items as  $date => $item) {
            $days = date('d-m-Y', strtotime($date));
            foreach ($item as $val) {

                $closing[$days]  = $val->closing_stock;
            }
        }

        return dateTimeFilter($closing);
    }
    /* weekly Inwarding Count*/
    public function OpeningShipmentCount(): array
    {

        $items = $this->connection->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $collection = [];

        foreach ($items as $date => $item) {

            foreach ($item as $datav) {

                $item_list = json_decode($datav->items, true);
                $days = date('d-m-Y', strtotime($date));
                foreach ($item_list as $key => $value) {
                    if (array_key_exists($days, $collection)) {

                        $collection[$days] += $value['quantity'];
                    } else {
                        $collection[$days] = $value['quantity'];
                    }
                }
            }
        }

        return dateTimeFilter($collection);
    }

    /* weekly Inwarding Amount*/
    public function InwardingAmount(): array
    {
        $openShipmentData = $this->connection->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $shipment_lists_date_wise = [];

        foreach ($openShipmentData as $key => $datewiseData) {

            foreach ($datewiseData as $items) {

                $item_lists = json_decode($items->items);

                foreach ($item_lists as $item) {

                    $days = date('d-m-Y', strtotime($key));

                    if (array_key_exists($days, $shipment_lists_date_wise)) {
                        $shipment_lists_date_wise[$days] += $item->quantity * $item->price;
                    } else {
                        $shipment_lists_date_wise[$days] = $item->quantity * $item->price;
                    }
                }
            }
        }

        return dateTimeFilter($shipment_lists_date_wise);
    }

    /* weekly Outwarding Count*/
    public function OutwardShipmentCount(): array
    {

        $items = $this->connection->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $collection = [];

        foreach ($items as $date => $item) {

            foreach ($item as $data) {

                $item_list = json_decode($data->items, true);
                foreach ($item_list as $key => $value) {
                    $days = date('d-m-Y', strtotime($date));

                    if (array_key_exists($days, $collection)) {
                        $collection[$days] +=  $value['quantity'];
                    } else {
                        $collection[$days] =  $value['quantity'];
                    }
                }
            }
        }

        return dateTimeFilter($collection);
    }

    /* weekly Outwarding Amount*/
    public function OutwardShipmentAmount(): array
    {

        $openShipmentData = $this->connection->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $shipment_out_date_wise = [];

        foreach ($openShipmentData as $key => $datewiseData) {

            foreach ($datewiseData as $items) {

                $item_lists = json_decode($items->items);

                foreach ($item_lists as $item) {

                    $days = date('d-m-Y', strtotime($key));

                    if (array_key_exists($days, $shipment_out_date_wise)) {
                        $shipment_out_date_wise[$days] += $item->quantity * $item->price;
                    } else {
                        $shipment_out_date_wise[$days] = $item->quantity * $item->price;
                    }
                }
            }
        }

        return dateTimeFilter($shipment_out_date_wise);
    }


    /* weekly closing stock */
    public function ClosingCount(): array
    {
        $items = $this->connection->table('stocks')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
        $closing = [];

        foreach ($items as  $date => $item) {
            $days = date('d-m-Y', strtotime($date));
            foreach ($item as $val) {

                $closing[$days]  = $val->closing_stock;
            }
        }
        return dateTimeFilter($closing);
        
    }

     

    /* weekly closing amount*/
    public function ClosingAmount()
    {
        $items = $this->connection->table('stocks')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
        $closing_amt = [];

        foreach ($items as  $date => $item) {
            $days = date('d-m-Y', strtotime($date));
            foreach ($item as $val) {

                $closing_amt[$days]  = $val->closing_amount;
            }
        }
        
        return dateTimeFilter($closing_amt);

    }
}
