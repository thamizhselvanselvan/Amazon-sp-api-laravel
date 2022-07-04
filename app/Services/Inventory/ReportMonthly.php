<?php

namespace App\Services\Inventory;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ReportMonthly
{

    private $connection = '';

    public function __construct()
    {
        $this->connection = DB::connection('inventory');
    }

    /* Monthly Inwarding Count*/
    public function MonthlyInCount(): array
    {

        $items = $this->connection->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(30))->get()->groupBy('created_at');

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
        return dateTimeFiltermonthly($collection);
        
    }

    /* Monthly Inwarding Amount*/
    public function MonthlyInAmount(): array
    {
        $openShipmentData = $this->connection->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(30))->get()->groupBy('created_at');

        $inw_amount = [];

        foreach ($openShipmentData as $key => $datewiseData) {

            foreach ($datewiseData as $items) {
                
                $item_lists = json_decode($items->items);
                
                foreach ($item_lists as $item) {
                    
                    $days = date('d-m-Y', strtotime($key));
                    
                    if (array_key_exists($days, $inw_amount)) {
                        $inw_amount[$days] += $item->quantity * $item->price;
                    } else {
                        $inw_amount[$days] = $item->quantity * $item->price;
                    }
                }
            }
        }

     
        return dateTimeFiltermonthly($inw_amount);
    }

    /* Monthly Outwarding Count*/
    public function monthly_out_count(): array
    {

        $items = $this->connection->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(30))->get()->groupBy('created_at');
        $out_count = [];

        foreach ($items as $date => $item) {

            foreach ($item as $data) {

                $item_list = json_decode($data->items, true);
                foreach($item_list as  $value)
                {
                $days = date('d-m-Y', strtotime($date));

                if (array_key_exists($days, $out_count)) {
                    $out_count[$days] +=  $value['quantity'];
                } else {
                    $out_count[$days] =  $value['quantity'];
                }
            }
            }
        }

        return dateTimeFiltermonthly($out_count);
    }

    /* Monthly Outwarding Amount*/
    public function monthly_out_amount(): array
    {

        $openShipmentData = $this->connection->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(30))->get()->groupBy('created_at');

        $shipment_out_amount = [];

        foreach ($openShipmentData as $key => $datewiseData) {

            foreach ($datewiseData as $items) {

                $item_lists = json_decode($items->items);

                foreach ($item_lists as $item) {

                    $days = date('d-m-Y', strtotime($key));

                    if (array_key_exists($days, $shipment_out_amount)) {
                        $shipment_out_amount[$days] += $item->quantity * $item->price;
                    } else {
                        $shipment_out_amount[$days] = $item->quantity * $item->price;
                    }
                }
            }
        }

        return dateTimeFiltermonthly($shipment_out_amount);
    }

          /* weekly closing stock */
    public function ClosingCountmonth(): array
    {
        $items = $this->connection->table('stocks')->where('created_at', '>=', Carbon::now()->subdays(30))->get()->groupBy('created_at');
        $closing_month = [];

        foreach ($items as  $date => $item) {
            $days = date('d-m-Y', strtotime($date));
            foreach ($item as $val) {

                $closing_month[$days]  = $val->closing_stock;
            }
        }
        return dateTimeFiltermonthly($closing_month);
        
    }

    /* Monthly closing amount*/
    public function ClosingAmountmonth()
    {
        $items = $this->connection->table('stocks')->where('created_at', '>=', Carbon::now()->subdays(30))->get()->groupBy('created_at');
        $closing_amt_month = [];

        foreach ($items as  $date => $item) {
            $days = date('d-m-Y', strtotime($date));
            foreach ($item as $val) {

                $closing_amt_month[$days]  = $val->closing_amount;
            }
        }
        
        return dateTimeFiltermonthly($closing_amt_month);

    }
}
