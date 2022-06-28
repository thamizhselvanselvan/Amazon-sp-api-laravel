<?php

namespace App\Http\Controllers\Inventory;

use DatePeriod;
use Carbon\Carbon;
use Nette\Utils\Json;
use League\Csv\Writer;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Inventory\Shipment;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Outshipment;
use Illuminate\Support\Facades\Storage;
use App\Services\Inventory\ReportWeekly;
use App\Http\Controllers\Inventory\CampaignHistory;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class ReportController extends Controller
{
    public function daily()
    {
        /* Wareouse */
        $ware_lists = Warehouse::get();

        /* Date */
        $date = Carbon::now()->format('d M Y');

        /* Inwarding count */
        $dayin =   Inventory::whereDate('created_at',  Carbon::today()->toDateString())->get();
        $todayinward = count($dayin);

        /* Outwarding count */
        $dayout =   Outshipment::whereDate('created_at',  Carbon::today()->toDateString())->get();
        $todayoutward = count($dayout);

        /* Opeaning Stock */
        $startTime = Carbon::today()->subDays(365);
        $endTimeYesterday = Carbon::yesterday()->endOfDay();
        $open = Inventory::whereBetween('created_at', [$startTime, $endTimeYesterday])->get();
        $todayopeningstock = count($open);


        /* Closing Stock count */
        $close =   Inventory::get();
        $todayclosingstock = count($close);

        /* Opeaning Amount */
        $amt = [];
        $openstockamt =   Inventory::whereBetween('created_at', [$startTime, $endTimeYesterday])->get();
        foreach ($openstockamt as $amt) {
            $singleprice[] = [
                'price' => $amt['price'],
                'qty' => $amt['quantity'],
                'total' => $amt['price'] * $amt['quantity'],
            ];
        }
        foreach ($singleprice as $sum) {
            $totalprice[] = $sum['total'];
        }
        $totalopenamt =  array_sum($totalprice);

        /* Day Inwarding Amount */

        $dayinamt =   Shipment::whereDate('created_at',  Carbon::today()->toDateString())->get();
        $totaldayinvamt = 0;
        foreach ($dayinamt as $key => $amtday) {

            $item_lists = json_decode($amtday->items);

            foreach ($item_lists as $item) {

                $totaldayinvamt += $item->quantity * $item->price;
            }
        }

        /* Outwarding Amount */

        $dayoutamt =   Outshipment::whereDate('created_at',  Carbon::today()->toDateString())->get();
        $totaldayoutamt = 0;
        foreach ($dayoutamt as $key => $amtdayout) {

            $item_lists = json_decode($amtdayout->items);

            foreach ($item_lists as $item) {

                $totaldayoutamt += $item->quantity * $item->price;
            }
        }

        /* Cloasing Amount */
        $closeamt =   Inventory::get();
        $closeprice = [];
        foreach ($closeamt as $close) {
            $closeprice[] = [
                'price' => $close['price'],
                'qty' => $close['quantity'],
                'total' => $close['price'] * $close['quantity'],
            ];
        }
        $dayclosing = [];
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
        // dd($data);
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

        /* weekly Inwarding Count*/
        $inward_count = $report_weekly->OpeningShipmentCount();


        /* weekly Inwarding  Amount*/
        $week_inv_amt = $report_weekly->InwardingAmount();


        /* weekly Outwarding  Count*/
        $week_out_count = $report_weekly->OutwardShipmentCount();



        /* weekly Outwarding  Amount*/
        $week_out_amt = $report_weekly->OutwardShipmentAmount();


        /* weekly closing count*/
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


    public function eportinvweekly(Request $request)
    {
        // $week_data = $this->getweekly($report_weekly);


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
        // $writer->insertAll($week_data);
        return Storage::download($exportFilePath);
    }

    public function Monthly(ReportWeekly $report_monthly)
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
        /* Monthly closing count*/

        /* weekly Inwarding Count*/
        $month_inv_count = $report_monthly->OpeningShipmentCount();


        /* weekly Inwarding  Amount*/
        $month_inv_amt = $report_monthly->InwardingAmount();


        /* weekly Outwarding  Count*/
        $month_out_count = $report_monthly->OutwardShipmentCount();



        /* weekly Outwarding  Amount*/
        $month_out_closing_amt = $report_monthly->OutwardShipmentAmount();


        /* weekly closing count*/
        $month_closing_count = $report_monthly->ClosingCount();

        /* weekly closing Amount*/
        $month_closing_amt = $report_monthly->ClosingAmount();


        $month_data = [];
        foreach ($date_arraymonth as $key => $val) {
            $week_data[] = [
                $val,
                 $month_closing_count[$key],
                $month_closing_amt[$key],
                $month_inv_count[$key],
                $month_inv_amt[$key],
                $month_out_count[$key],
                $month_out_closing_amt[$key],
                $month_closing_count[$key],
                $month_closing_amt[$key]
            ];
        }

        return $month_data;
    }

    public function eportinvmonthly(Request $request)
    {
        // $month_data = $this->getMonthly();

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
        $exportFilePath = 'Inventory/MonthlyReport.csv'; // your file path, where u want to save
        if (!Storage::exists($exportFilePath)) {
            Storage::put($exportFilePath, '');
        }
        $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
        $writer->insertOne($headers);

        $csv_value = [];
        $count = 0;
        // $writer->insertAll($month_data);
        return Storage::download($exportFilePath);
    }
}
