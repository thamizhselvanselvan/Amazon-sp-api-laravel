<?php

namespace App\Services\Inventory;

use DB;
use Carbon\Carbon;
use Carbon\CarbonInterval;

class ReportWeekly {

  private $connection = '';

  public function __construct() {
    $this->connection = DB::connection('inventory');
  }

  public function OpeningShipmentCount() : array {

    $items = $this->connection->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

    $collection = [];

    foreach ($items as $date => $item) {

        foreach ($item as $data) {

            $item_list = json_decode($data->items, true);

            $days = date('d-m-Y', strtotime($date));

            if(array_key_exists($days, $collection)) {
                $collection[$days] += count($item_list);
            } else {
                $collection[$days] = count($item_list);
            }

        }
    }

    return dateTimeFilter($collection);
  }

  public function InwardingAmount () : array {
    /* weekly Inwarding Amount*/
    $openShipmentData = $this->connection->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

    $shipment_lists_date_wise = [];

    foreach ($openShipmentData as $key => $datewiseData) {

        foreach ($datewiseData as $items) {

            $item_lists = json_decode($items->items);

            foreach ($item_lists as $item) {

                $days = date('d-m-Y', strtotime($key));

                if(array_key_exists($days, $shipment_lists_date_wise)) {
                    $shipment_lists_date_wise[$days] += $item->quantity * $item->price;
                } else {
                    $shipment_lists_date_wise[$days] = $item->quantity * $item->price;
                }

            }
        }
    }

    return dateTimeFilter($shipment_lists_date_wise);
  }

  public function OutwardShipmentCount() : array {
    $outShipmentcount = $this->connection->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

    $out_count_date_wise = [];

    foreach ($outShipmentcount as $key => $datewiseDataCount) {

        foreach ($datewiseDataCount as $items) {
            $item_list = json_decode($items->items);

            $days = date('d-m-Y', strtotime($key));

            if(array_key_exists($days, $out_count_date_wise)) {
                $out_count_date_wise[$days] += count($item_list);
            } else {
                $out_count_date_wise[$days] = count($item_list);
            }

        }
    }

    $week_out_count = dateTimeFilter(6, $out_count_date_wise);
  }

  public function OutwardShipmentAmount() :array {
    $outShipmentData = $this->connection->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(7))->get()->groupBy('created_at');

    $out_shipment_lists_date_wise = [];

    foreach ($outShipmentData as $key => $datewisecount) {

        foreach ($datewisecount as $items) {

            $item_list = json_decode($items->items);

            foreach ($item_list as $item) {

                $days = date('d-m-Y', strtotime($key));

                if(array_key_exists($days, $out_shipment_lists_date_wise)) {
                    $out_shipment_lists_date_wise[$days] += $item->quantity * $item->price;
                } else {
                    $out_shipment_lists_date_wise[$days] = $item->quantity * $item->price;
                }

            }
        }
    }

    $week_out_amt  = dateTimeFilter(6, $out_shipment_lists_date_wise);
  }

  public function ClosingCount() {
    $week_close_cnt = $this->connection->table('inventory')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
    $shipment_closing = [];

    foreach ($week_close_cnt as $key => $closingData) {

        foreach ($closingData as $items) {

            $days = date('d-m-Y', strtotime($key));

                if(array_key_exists($days, $shipment_closing)) {
                    $shipment_closing[$days] += count($item_list);
                } else {
                    $shipment_closing[$days] = count($item_list);
                }
        }
    }

    $week_closing_count = dateTimeFilter(6, $shipment_closing);
  }

  public function ClosingAmount() {
    $week_close_amount = $this->connection->table('inventory')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
    $shipment_closing = [];

    foreach ($week_close_amount as $key => $closingData) {

        foreach ($closingData as $items) {

            $days = date('d-m-Y', strtotime($key));

                if(array_key_exists($days, $shipment_closing)) {
                    $shipment_closing[$days] += $items->quantity * $items->price;
                } else {
                    $shipment_closing[$days] = $items->quantity * $items->price;
                }
        }
    }

    $week_closing_amt = dateTimeFilter(6, $shipment_closing);
  }

}
