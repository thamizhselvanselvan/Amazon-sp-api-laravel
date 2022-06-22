<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Outshipment;
use App\Models\Inventory\Shipment;

class ReportController extends Controller
{
    public function index(request $request)
    {
        $ware_lists = Warehouse::get();
      
        $first =  Inventory::whereDate('created_at',  Carbon::today()->toDateString())->get()->first();
        $date = ($first->created_at)->format('M d Y');

        $dayin =   Inventory::whereDate('created_at',  Carbon::today()->toDateString())->get();
        $todayinward = count($dayin);

        $dayout =   Outshipment::whereDate('created_at',  Carbon::today()->toDateString())->get();
        $todayoutward = count($dayout);

        $open =   Inventory::whereDate('created_at',  Carbon::yesterday()->toDateString())->get();
        $todayopeningstock = count($open);

        $close =   Inventory::whereDate('created_at',  Carbon::now()->toDateString())->get();
        $todayclosingstock = count($close);

        $amt = [];
        $openstockamt =   Inventory::get();
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
        foreach ($dayinamt as $amtday) {
            $daysingleprice[] = [
                'price' => $amtday['price'],
                'qty' => $amtday['quantity'],
                'total' => $amtday['price'] * $amtday['quantity'],
            ];
        }

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



        $closeamt =   Inventory::whereDate('created_at',  Carbon::now()->toDateString())->get();
        foreach ($closeamt as $close) {
            $closeprice[] = [
                'price' => $close['price'],
                'qty' => $close['quantity'],
                'total' => $close['price'] * $close['quantity'],
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
            "inwarded" => $todayinward,
            "tdy_inv_amt" => $totaldayinvamt,
            "outwarded" => $todayoutward,
            "tdy_out_amt" => $totaldayoutamt,
            "closing_stock" => $todayclosingstock,
            "closing_amt" => $dayclosingamt
        ];




        return view('inventory.report.report', compact('ware_lists', 'data'));
    }
}
