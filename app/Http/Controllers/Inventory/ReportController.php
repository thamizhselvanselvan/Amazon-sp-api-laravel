<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Inventory\Shipment;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Outshipment;

class ReportController extends Controller
{
    public function daily()
    {
        $ware_lists = Warehouse::get();

        $date = Carbon::now()->format('d M Y');

        $dayin =   Inventory::whereDate('created_at',  Carbon::today()->toDateString())->get();
        $todayinward = count($dayin);

        $dayout =   Outshipment::whereDate('created_at',  Carbon::today()->toDateString())->get();
        $todayoutward = count($dayout);

        $startTime = Carbon::today()->subDays(365);
        $endTimeYesterday = Carbon::yesterday()->endOfDay();
        $open = Inventory::whereBetween('created_at', [$startTime, $endTimeYesterday])->get();
        $todayopeningstock = count($open);

        $close =   Inventory::get();
        $todayclosingstock = count($close);

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



        $dayinamt =   Inventory::whereDate('created_at',  Carbon::today()->toDateString())->get();
        $daysingleprice = [];
        foreach ($dayinamt as $amtday) {
            $daysingleprice[] = [
                'price' => $amtday['price'],
                'qty' => $amtday['quantity'],
                'total' => $amtday['price'] * $amtday['quantity'],
            ];
        }

        $daytotalprice = [];
        foreach ($daysingleprice as $daysum) {
            $daytotalprice[] = $daysum['total'];
        }
        $totaldayinvamt =  array_sum($daytotalprice);


        $daysingleoutprice = [];
        $dayoutamt =   Outshipment::whereDate('created_at',  Carbon::today()->toDateString())->get();
        foreach ($dayoutamt as $amtdayout) {
            $daysingleoutprice[] = [
                'price' => $amtdayout['price'],
                'qty' => $amtdayout['quantity'],
                'total' => $amtdayout['price'] * $amtdayout['quantity'],
            ];
        }
        $dayouttotprice = [];
        foreach ($daysingleoutprice as $dayoutsum) {
            $dayouttotprice[] = $dayoutsum['total'];
        }
        $totaldayoutamt =  array_sum($dayouttotprice);



        $closeamt =   Inventory::get();
        //  dd( $closeamt);
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




        return view('inventory.report.daily', compact('ware_lists', 'data'));
    }


    public function weekly()
    {

        $ware_lists = Warehouse::get();
        return view('inventory.report.weekly', compact('ware_lists'));
    }
}
