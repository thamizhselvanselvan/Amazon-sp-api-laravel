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


    /* weekly Inwarding Count*/
    public function OpeningShipmentCount(): array
    {

        $items = $this->connection->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $collection = [];
        
        foreach ($items as $date => $item) {

            foreach ($item as $data) {

                $item_list = json_decode($data->items, true);
                $days = date('d-m-Y', strtotime($date));
                    foreach($item_list as $key => $value)
                    {
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
                foreach($item_list as $key => $value)
                {
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

        $end_date =  Carbon::now()->subDay(1);
        $end_date = $end_date->toDateString();
        $end_date = $end_date . ' 23:59:59';

        $week_close_count = $this->connection->table('inventory')->where('created_at', '<=', $end_date)->get();
        $week_close_count = collect($week_close_count);
        $week_close_count = $week_close_count->groupBy('updated_at');

        $close_count = [];
        $days = 7;
        
        // foreach ($week_close_count as $key => $week_close_details) {

        //     $start_date = Carbon::now()->subDay($days);
        //     $start_date = $start_date->toDateString();
        //     $start_date = $start_date . ' 00:00:01';

        //     $end_date =  Carbon::now()->subDay($days);
        //     $end_date = $end_date->toDateString();
        //     $end_date = $end_date . ' 23:59:59';

        //     echo 'Start Date' . $start_date . '<br>';
        //     echo 'End Date ' . $end_date;
        //     echo "<hr>";
        //     $days--;

        //     // echo $key;
        //     // dd($week_close_details);
        // }
        // exit;
        foreach ($week_close_count as $key => $closingcount) {
            foreach ($closingcount as $items) {

                $days = date('d-m-Y', strtotime($key));

                if (array_key_exists($days, $close_count)) {
                    $close_count[$days] += count($items);
                } else {
                    // $close_count[$days] =  count($items);
                }
            }
        }
        return dateTimeFilter($close_count);
    }

    /* weekly closing amount*/
    public function ClosingAmount()
    {
        $week_close_amount = $this->connection->table('inventory')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
        $shipment_closing = [];

        foreach ($week_close_amount as $key => $closingData) {

            foreach ($closingData as $items) {

                $days = date('d-m-Y', strtotime($key));

                if (array_key_exists($days, $shipment_closing)) {
                    $shipment_closing[$days] += $items->quantity * $items->price;
                } else {
                    $shipment_closing[$days] = $items->quantity * $items->price;
                }
            }
        }

        return dateTimeFilter($shipment_closing);
    }
}
