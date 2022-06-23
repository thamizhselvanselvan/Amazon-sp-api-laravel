<?php

namespace App\Http\Controllers\Inventory;

use DatePeriod;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Inventory\Shipment;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Outshipment;
use App\Http\Controllers\Inventory\CampaignHistory;
use Nette\Utils\Json;
use Symfony\Component\Serializer\Encoder\JsonDecode;

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


        $date_array = [];
        $i = 0;
        while ($i < 7) {
            $today = Carbon::today();
            array_push($date_array, $today->subDays($i)->format('Y-m-d'));
            $i++;
        }

        $weekclose = Inventory::whereBetween('created_at', [Carbon::now()->subDays(7)->format('Y-m-d') . " 00:00:00", Carbon::now()->format('Y-m-d') . " 23:59:59"])
            ->groupBy('created_at')
            ->orderBy('created_at')
            ->get([
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'),
                DB::raw('count(*) as total')
            ])
            ->keyBy('date')
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date);
                return $item;
            });



        $period = new DatePeriod(Carbon::now()->subDays(6), CarbonInterval::day(), Carbon::today()->endOfDay());

        $weeklyin = array_map(function ($datePeriod) use ($weekclose) {
            $date = $datePeriod->format('Y-m-d');
            return $weekclose->has($date) ? $weekclose->get($date)->total : 0;
        }, iterator_to_array($period));
        $weekclosing = array_reverse($weeklyin);


        //         $weekcloseamt = Inventory::whereBetween('created_at', [Carbon::now()->subDays(7)->format('Y-m-d') . " 00:00:00", Carbon::now()->format('Y-m-d') . " 23:59:59"])
        //         ->groupBy('created_at')
        //         ->orderBy('created_at')
        //         ->get([
        //             DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as date'),
        //                 DB::raw('count(*) as total'),
        //             DB::raw('SUM(price) as totprice'),
        //             DB::raw('total(quantity) as q'),
        //         ])

        //         ->keyBy('date')
        //         ->map(function ($item) {
        //             $item->date = Carbon::parse($item->date);
        //             return $item;
        //         });

        //     $period = new DatePeriod(Carbon::now()->subDays(6), CarbonInterval::day(), Carbon::today()->endOfDay());

        //     $weeklyin = array_map(function ($datePeriod) use ($weekcloseamt) {
        //         $date = $datePeriod->format('Y-m-d');
        //         return $weekcloseamt->has($date) ? $weekcloseamt->get($date)->total : 0;
        //     }, iterator_to_array($period));
        //     // $weekclosing = array_reverse($weekcloseamt);

        //  dd($weekcloseamt);

        // $startTime = Carbon::today()->subDays(7);
        // $endTime = Carbon::today();
        // $open =Inventory::whereBetween('created_at', [Carbon::now()->subDays(7)->format('Y-m-d') . " 00:00:00", Carbon::now()->format('Y-m-d') . " 23:59:59"])
        // ->get();


        //    $open =Shipment::whereBetween('created_at', [Carbon::now()->subDays(7) . " 00:00:00", Carbon::now(). " 23:59:59"])->get();


        $openShipmentData = DB::connection('inventory')->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(7))->get()->groupBy('created_at');

        $shipment_lists_date_wise = [];

        foreach ($openShipmentData as $key => $datewiseData) {

            foreach ($datewiseData as $items) {

                $item_lists = json_decode($items->items);

                foreach ($item_lists as $item) {

                    $shipment_lists_date_wise[date('d-m-Y', strtotime($key))] = $item->quantity * $item->price;

                    $period = new DatePeriod(Carbon::now()->subDays(6), CarbonInterval::day(), Carbon::today()->endOfDay());

                    $weeklyin = array_map(function ($datePeriod) use ($shipment_lists_date_wise) {
                        $date = $datePeriod->format('d-m-Y');
                        return (isset($shipment_lists_date_wise[$date])) ? $shipment_lists_date_wise[$date] : 0;
                    }, iterator_to_array($period));
                    $week_closing_amt = array_reverse($weeklyin);

                }
            }
        }


   
        dd($date_array, $weekclosing, $week_closing_amt);
        return view('inventory.report.weekly', compact('ware_lists'));
    }
}
