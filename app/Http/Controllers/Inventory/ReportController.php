<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\Http\Request;
use App\Models\Inventory\Stocks;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Services\Inventory\ReportWeekly;
use Yajra\DataTables\Facades\DataTables;
use App\Services\Inventory\ReportMonthly;
use App\Models\Inventory\Shipment_Inward_Details;
use App\Models\Inventory\Shipment_Outward_Details;

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
    public function index(Request $request)
    {
        $ware_lists = Warehouse::get();
        if ($request->ajax()) {
            $startTime = Carbon::today()->subDays(7);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();
            $data = Stocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->orderBy('id', 'DESC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }


        return view('inventory.report.weekly', compact('ware_lists'));
    }

    public function eportinvweekly()
    {
        $startTime = Carbon::today()->subDays(7);
        $endTimeYesterday = Carbon::yesterday()->endOfDay();

        $week_data =  Stocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
            ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
            ->orderBy('id', 'DESC')
            ->get();


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
        $exportFilePath = 'Inventory/weeklyReport.csv';
        if (!Storage::exists($exportFilePath)) {
            Storage::put($exportFilePath, '');
        }
        $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
        $writer->insertOne($headers);


        $writer->insertAll($week_data->toarray());
        return Storage::download($exportFilePath);
    }



    public function monthlyview(Request $request)
    {
        $ware_lists = Warehouse::get();
        if ($request->ajax()) {
            $startTime = Carbon::today()->subDays(31);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();
            $data = Stocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->orderBy('id', 'DESC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }


        return view('inventory.report.monthly', compact('ware_lists'));
    }
    public function eportinvmonthly()
    {
        $startTime = Carbon::today()->subDays(31);
        $endTimeYesterday = Carbon::yesterday()->endOfDay();
        $records = [];
        $records =  Stocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
            ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
            ->orderBy('id', 'DESC')
            ->get();

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

        $writer->insertAll($records->toArray());
        return Storage::download($exportFilePath);
    }
}
