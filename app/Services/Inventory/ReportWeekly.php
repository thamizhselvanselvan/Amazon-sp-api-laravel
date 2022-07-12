<?php

namespace App\Services\Inventory;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


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

        $items = $this->connection->table('shipment_inward_details')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
        $closing = [];

        foreach ($items as  $date => $item) {
            $days = date('d-m-Y', strtotime($date));
            foreach ($item as $val) {
                $closing[$days]  = $val->quantity;
            }
        }

        return dateTimeFilter($closing);
    }
    /* weekly Inwarding Count*/
    public function InwardingCount(): array
    {
        $items = $this->connection->table('shipment_inward_details')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $collection = [];

        foreach ($items as $date => $item) {

            foreach ($item as $data) {

                $days = date('d-m-Y', strtotime($date));

                if (array_key_exists($days, $collection)) {
                    $collection[$days] +=  $data->quantity;
                } else {
                    $collection[$days] =  $data->quantity;
                }
            }
        }

        return dateTimeFilter($collection);
    }
    /* weekly Inwarding Amount*/
    public function InwardingAmount(): array
    {
        $openShipmentData = $this->connection->table('shipment_inward_details')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $shipment_lists_date_wise = [];

        foreach ($openShipmentData as $key => $datewiseData) {

            foreach ($datewiseData as $items) {

                $days = date('d-m-Y', strtotime($key));

                if (array_key_exists($days, $shipment_lists_date_wise)) {
                    $shipment_lists_date_wise[$days] += $items->quantity * $items->price;
                } else {
                    $shipment_lists_date_wise[$days] = $items->quantity * $items->price;
                }
            }
        }
        return dateTimeFilter($shipment_lists_date_wise);
    }

    /* weekly Outwarding Count*/
    public function OutwardShipmentCount(): array
    {

        $items = $this->connection->table('shipments_outward_details')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $collection = [];

        foreach ($items as $date => $item) {

            foreach ($item as $data) {

                $days = date('d-m-Y', strtotime($date));

                if (array_key_exists($days, $collection)) {
                    $collection[$days] +=  $data->quantity;
                } else {
                    $collection[$days] =  $data->quantity;
                }
            }
        }
        return dateTimeFilter($collection);
    }

    /* weekly Outwarding Amount*/
    public function OutwardShipmentAmount(): array
    {

        $openShipmentData = $this->connection->table('shipments_outward_details')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $shipment_out_date_wise = [];

        foreach ($openShipmentData as $key => $datewiseData) {

            foreach ($datewiseData as $item) {

                $days = date('d-m-Y', strtotime($key));

                if (array_key_exists($days, $shipment_out_date_wise)) {
                    $shipment_out_date_wise[$days] += $item->quantity * $item->price;
                } else {
                    $shipment_out_date_wise[$days] = $item->quantity * $item->price;
                }
            }
        }

        return dateTimeFilter($shipment_out_date_wise);
    }
    /* weekly closing stock */
    public function ClosingCount(): array
    {
        $itemsin = $this->connection->table('shipment_inward_details')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
        $collectionin = [];

        foreach ($itemsin as $date => $item) {

            foreach ($item as $data) {

                $days = date('d-m-Y', strtotime($date));

                if (array_key_exists($days, $collectionin)) {
                    $collectionin[$days] +=  $data->quantity;
                } else {
                    $collectionin[$days] =  $data->quantity;
                }
            }
        }
        $itemsout = $this->connection->table('shipments_outward_details')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $collectionout = [];

        foreach ($itemsout as $date => $item) {

            foreach ($item as $data) {

                $days = date('d-m-Y', strtotime($date));

                if (array_key_exists($days, $collectionout)) {
                    $collectionout[$days] +=  $data->quantity;
                } else {
                    $collectionout[$days] =  $data->quantity;
                }
            }
        }
        $closingstock = [];
        foreach($collectionin as $key => $value)
        {
            $closingstock[$key] = $value - $collectionout[$key];
        }

        return dateTimeFilter($closingstock);
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
