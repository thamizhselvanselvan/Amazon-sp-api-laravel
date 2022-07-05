<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use League\Csv\Writer;
use App\Models\Inventory\Shipment;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Outshipment;
use Illuminate\Support\Facades\Storage;
use App\Services\Inventory\ReportWeekly;
use App\Models\Inventory\Shipment_Inward;
use App\Services\Inventory\ReportMonthly;
use App\Models\Inventory\Shipment_Inward_Details;
use App\Models\inventory\Shipment_Outward_Details;

class ReportController extends Controller
{
    public function daily()
    {
        /* Wareouse */
        $ware_lists = Warehouse::get();

        /* Date */
        $date = Carbon::now()->format('d M Y');

        /* Inwarding count */
        $todayinward = 0;
        $dayin =  Shipment_Inward_Details::whereDate('created_at',  Carbon::today()->toDateString())->get();
        foreach ($dayin as $key => $item) {

            $todayinward += $item['quantity'];
        }

        /* Outwarding count */
        $todayoutward = 0;
        $dayout =   Shipment_Outward_Details::whereDate('created_at',  Carbon::today()->toDateString())->get();
        foreach ($dayout as $key => $value) {

            $todayoutward += $value['quantity'];
        }

        /* Opeaning Stock */
        $todayopening = 0;
        $startTime = Carbon::today()->subDays(365);
        $endTimeYesterday = Carbon::yesterday()->endOfDay();
        // $open = Inventory::whereBetween('created_at', [$startTime, $endTimeYesterday])->get();
        // foreach ($open as  $data) {
        //     $todayopeningstock +=  $data['balance_quantity'];
        // }
        $open = Shipment_Inward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])->get();
        foreach ($open as  $data) {
            $todayopening +=  $data['quantity'];
        }
        $todayoutstock = 0;
        $close =  Shipment_Outward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])->get();
        foreach ($close as  $cdata) {
            $todayoutstock +=  $cdata['quantity'];
        }

        $todayopeningstock =  $todayopening - $todayoutstock;

        /* Closing Stock count */
        $startTimecls = Carbon::today()->subDays(365);
        $endTime = Carbon::now();

        $todayclosingstock = 0;
        $close =  Inventory::whereBetween('created_at', [$startTimecls, $endTime])->get();

        foreach ($close as  $data) {
            $todayclosingstock +=  $data['balance_quantity'];
        }

        /* Opeaning Amount */
        $amt = [];
        $openamtamt =  Shipment_Inward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])->get();
        foreach ($openamtamt as $amt) {
            $singlepricein[] = [
                'price' => $amt['price'],
                'qty' => $amt['quantity'],
                'total' => $amt['price'] * $amt['quantity'],
            ];
        }
        $closeamt =  Shipment_Outward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])->get();
        foreach ($closeamt as $amt) {
            $singlepriceout[] = [
                'price' => $amt['price'],
                'qty' => $amt['quantity'],
                'total' => $amt['price'] * $amt['quantity'],
            ];
        }

        foreach ($singlepricein as $sum) {
            $totalpricein[] = $sum['total'];
        }
        foreach ($singlepriceout as $sumclose) {
            $totalpriceout[] = $sumclose['total'];
        }

        $totalinamt =  array_sum($totalpricein);
        $totalotamt = array_sum($totalpriceout);

        $totalopenamt =   $totalinamt  -  $totalotamt;

        /* Day Inwarding Amount */

        $dayinamt =   Shipment_Inward_Details::whereDate('created_at',  Carbon::today()->toDateString())->get();
        $totaldayinvamt = 0;

        foreach ($dayinamt as $key => $amtday) {

            $totaldayinvamt += $amtday->quantity * $amtday->price;
        }

        /* Outwarding Amount */

        $dayoutamt =   Shipment_Outward_Details::whereDate('created_at',  Carbon::today()->toDateString())->get();
        $totaldayoutamt = 0;
        foreach ($dayoutamt as $key => $amtdayout) {


            $totaldayoutamt += $amtdayout->quantity * $amtdayout->price;
        }

        /* Cloasing Amount */
        $closeamt =   Inventory::get();
        $closeprice = [];
        $dayclosing = [];
        foreach ($closeamt as $close) {
            $closeprice[] = [
                'price' => $close['price'],
                'qty' => $close['quantity'],
                'total' => $close['price'] * $close['balance_quantity'],
            ];
        }
        foreach ($closeprice as $dayclose) {

            $dayclosing[] = $dayclose['total'];
        }
        $dayclosingamt =  array_sum($dayclosing);


        $data = [
            "date" => $date,
            "open_stock" => $todayopeningstock,
            "open_stock_amt" => $totalopenamt,
            "inwarded" => $todayinward ? $todayinward : 0,
            "tdy_inv_amt" => $totaldayinvamt,
            "outwarded" => $todayoutward,
            "tdy_out_amt" => $totaldayoutamt,
            "closing_stock" => $todayclosingstock,
            "closing_amt" => $dayclosingamt
        ];

        return view('inventory.report.daily', compact('ware_lists', 'data'));
    }

    public function  weekly(ReportWeekly $report_weekly)
    {
        $ware_lists = Warehouse::get();
        $week_data = $this->getweekly($report_weekly);

        return view('inventory.report.weekly', compact('ware_lists', 'week_data'));
    }

    public function getweekly($report_weekly)
    {
        //Week date //
        $date_array = [];
        $i = 0;
        while ($i < 7) {
            $today = Carbon::today();
            array_push($date_array, $today->subDays($i)->format('Y-m-d'));
            $i++;
        }


        /* weekly opeanig Count*/
        $open_count = $report_weekly->OpeningStock();
        //  dd($open_count);
        /* weekly Inwarding Count*/
        $inward_count = $report_weekly->OpeningShipmentCount();


        /* weekly Inwarding  Amount*/
        $week_inv_amt = $report_weekly->InwardingAmount();


        /* weekly Outwarding  Count*/
        $week_out_count = $report_weekly->OutwardShipmentCount();



        /* weekly Outwarding  Amount*/
        $week_out_amt = $report_weekly->OutwardShipmentAmount();


        /* weekly closing count*/
        /* weekly opeanig Count*/
        $week_closing_count = $report_weekly->ClosingCount();

        /* weekly closing Amount*/
        $week_closing_amt = $report_weekly->ClosingAmount();



        $week_data = [];
        foreach ($date_array as $k => $val) {
            $week_data[] = [
                $val,
                $week_closing_count[$k],
                $week_closing_amt[$k],
                $inward_count[$k],
                $week_inv_amt[$k],
                $week_out_count[$k],
                $week_out_amt[$k],
                $week_closing_count[$k],
                $week_closing_amt[$k]
            ];
        }

        return $week_data;
    }

    public function eportinvweekly(ReportWeekly $report_weekly)
    {
        $week_exp = $this->getweekly($report_weekly);


        $headers = [
            'Date',
            'Opening Stock',
            'Opening Stock Amount',
            'Inv Inwarded',
            'Amount',
            'Inv Outwarded',
            'Amount',
            'Closing Stock',
            'closing Stock Amount'
        ];
        $exportFilePath = 'Inventory/weeklyReport.csv'; // your file path, where u want to save
        if (!Storage::exists($exportFilePath)) {
            Storage::put($exportFilePath, '');
        }
        $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
        $writer->insertOne($headers);

        $csv_value = [];
        $count = 0;
        $writer->insertAll($week_exp);
        return Storage::download($exportFilePath);
    }

    public function Monthly(ReportMonthly $report_monthly)
    {
        $ware_lists = Warehouse::get();
        $month_data = $this->getMonthly($report_monthly);
        return view('inventory.report.monthly', compact('ware_lists', 'month_data'));
    }

    public function getMonthly($report_monthly)
    {
        //Monthly date //
        $date_arraymonth = [];
        $i = 0;
        while ($i < 31) {
            $today = Carbon::today();
            array_push($date_arraymonth, $today->subDays($i)->format('Y-m-d'));
            $i++;
        }

        /* Monthly Inwarding Count*/
        $month_inv_count = $report_monthly->MonthlyInCount();


        /* Monthly Inwarding  Amount*/
        $month_inv_amt = $report_monthly->MonthlyInAmount();

        /* Monthly Outwarding  Count*/
        $month_out_count = $report_monthly->monthly_out_count();



        /* Monthly Outwarding  Amount*/
        $month_out_amt = $report_monthly->monthly_out_amount();


        /* Monthly closing count*/
        $month_closing_count = 0;
        $month_closing_count = $report_monthly->ClosingCountmonth();

        /* Monthly closing Amount*/
        $month_closing_amt = 0;
        $month_closing_amt = $report_monthly->ClosingAmountmonth();

        // dd($date_arraymonth, $month_inv_count,$month_inv_amt,$month_out_count,$month_out_amt,$month_closing_count,$month_closing_amt);
        $month_data = [];
        foreach ($date_arraymonth as $key => $val) {
            $month_data[] = [
                $val,
                $month_closing_count[$key],
                $month_closing_amt[$key],
                $month_inv_count[$key],
                $month_inv_amt[$key],
                $month_out_count[$key],
                $month_out_amt[$key],
                $month_closing_count[$key],
                $month_closing_amt[$key]
            ];
        }
        //  dd($month_data);
        return $month_data;
    }

    public function eportinvmonthly(ReportMonthly $report_monthly)
    {

        $month_exp = $this->getMonthly($report_monthly);

        $headers = [
            'Date',
            'Opening Stock',
            'Opening Stock Amount',
            'Inv Inwarded',
            'Amount',
            'Inv Outwarded',
            'Amount',
            'Closing Stock',
            'closing Stock  Amount'
        ];
        $exportFilePath = 'Inventory/MonthlyReport.csv';
        if (!Storage::exists($exportFilePath)) {
            Storage::put($exportFilePath, '');
        }
        $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
        $writer->insertOne($headers);

        $csv_value = [];
        $count = 0;
        $writer->insertAll($month_exp);
        return Storage::download($exportFilePath);
    }
}
