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
use App\Http\Controllers\Inventory\CampaignHistory;
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

        $ware_lists = Warehouse::get();

        //Week date //
        $date_array = [];
        $i = 0;
        while ($i < 7) {
            $today = Carbon::today();
            array_push($date_array, $today->subDays($i)->format('Y-m-d'));
            $i++;
        }
        /* weekly closing count*/

        $weekclose = Inventory::whereBetween('created_at', [Carbon::now()->subDays(6)->format('Y-m-d') . " 00:00:00", Carbon::now()->format('Y-m-d') . " 23:59:59"])
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
        $week_inv_count = array_reverse($weeklyin);


        /* weekly closing Amount*/
        $week_close_amount = DB::connection('inventory')->table('inventory')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
        $shipment_closing = [];

        foreach ($week_close_amount as $key => $closingData) {

            foreach ($closingData as $items) {


                $shipment_closing[date('d-m-Y', strtotime($key))] = $items->quantity * $items->price;



                $period = new DatePeriod(Carbon::now()->subDays(6), CarbonInterval::day(), Carbon::today()->endOfDay());

                $weeklycloseamt = array_map(function ($datePeriod) use ($shipment_closing) {
                    $date = $datePeriod->format('d-m-Y');
                    return (isset($shipment_closing[$date])) ? $shipment_closing[$date] : 0;
                }, iterator_to_array($period));
                $week_inv_amt = array_reverse($weeklyin);

                $week_closing_amt = array_reverse($weeklycloseamt);
            }
        }


        /* weekly Inwarding Amount*/
        $openShipmentData = DB::connection('inventory')->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

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
                    $week_inv_amt = array_reverse($weeklyin);
                }
            }
        }



        /* weekly Inwarding Count*/
        $openShipmentcount = DB::connection('inventory')->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $shipment_count_date_wise = [];

        foreach ($openShipmentcount as $key => $datewiseDataCount) {

            foreach ($datewiseDataCount as $items) {

                $item_list = json_decode($items->items);

                foreach ($item_list as $item) {

                    $shipment_count_date_wise[date('d-m-Y', strtotime($key))] = $item->quantity;
                    $period = new DatePeriod(Carbon::now()->subDays(6), CarbonInterval::day(), Carbon::today()->endOfDay());

                    $weeklyincount = array_map(function ($datePeriod) use ($shipment_count_date_wise) {
                        $date = $datePeriod->format('d-m-Y');
                        return (isset($shipment_count_date_wise[$date])) ? $shipment_count_date_wise[$date] : 0;
                    }, iterator_to_array($period));
                    $week_closing_count = array_reverse($weeklyincount);
                }
            }
        };


        /* weekly Outwarding  Amount*/
        $outShipmentData = DB::connection('inventory')->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(7))->get()->groupBy('created_at');

        $out_shipment_lists_date_wise = [];
        $week_out_closing_amt = [];
        foreach ($outShipmentData as $key => $datewisecount) {

            foreach ($datewisecount as $items) {

                $item_list = json_decode($items->items);

                foreach ($item_list as $item) {

                    $out_shipment_lists_date_wise[date('d-m-Y', strtotime($key))] = $item->quantity * $item->price;

                    $period = new DatePeriod(Carbon::now()->subDays(6), CarbonInterval::day(), Carbon::today()->endOfDay());

                    $weeklyout = array_map(function ($datePeriod) use ($out_shipment_lists_date_wise) {
                        $date = $datePeriod->format('d-m-Y');
                        return (isset($out_shipment_lists_date_wise[$date])) ? $out_shipment_lists_date_wise[$date] : 0;
                    }, iterator_to_array($period));
                    $week_out_closing_amt = array_reverse($weeklyout);
                }
            }
        }


        /* weekly Outwarding  Count*/
        $outShipmentcount = DB::connection('inventory')->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(7))->get()->groupBy('created_at');

        $outshipment_count_date_wise = [];

        foreach ($outShipmentcount as $key => $outwiseDataCount) {

            foreach ($outwiseDataCount as $items) {

                $item_list = json_decode($items->items);

                foreach ($item_list as $item) {

                    $outshipment_count_date_wise[date('d-m-Y', strtotime($key))] = $item->quantity;

                    $period = new DatePeriod(Carbon::now()->subDays(6), CarbonInterval::day(), Carbon::today()->endOfDay());

                    $weeklyoutcount = array_map(function ($datePeriod) use ($outshipment_count_date_wise) {
                        $date = $datePeriod->format('d-m-Y');
                        return (isset($outshipment_count_date_wise[$date])) ? $outshipment_count_date_wise[$date] : 0;
                    }, iterator_to_array($period));
                    $week_out_count = array_reverse($weeklyoutcount);
                }
            }
        }

        $week_data = [];
        foreach ($date_array as $k => $val) {
            $week_data[] = [
                $val,
                $week_closing_count[$k],
                $week_closing_amt[$k],
                $week_inv_count[$k],
                $week_inv_amt[$k],
                $week_out_count[$k],
                $week_out_closing_amt[$k],
                $week_closing_count[$k],
                $week_closing_amt[$k]
            ];
        }

        // $week_data = [
        //     "date" => $date_array,
        //     "open_stock" => $week_closing_count,
        //     "open_stock_amt" => $week_closing_amt,
        //     "inwarded" => $week_inv_count,
        //     "tdy_inv_amt" => $week_inv_amt,
        //     "outwarded" => $week_out_count,
        //     "tdy_out_amt" => $week_out_closing_amt,
        //     "closing_stock" => $week_closing_count,
        //     "closing_amt" => $week_closing_amt
        // ];
        return view('inventory.report.weekly', compact('ware_lists', 'week_data'));
    }

    public function eportinvweekly(Request $request)
    {
        $records = []; //Data from database
        $records = Inventory::query()
        ->select('warehouses.name', 'inventory.ship_id', 'inventory.asin', 'inventory.item_name', 'inventory.price', 'inventory.quantity', 'inventory.created_at', 'inventory.bin')
        ->join('shipments', function($query) {
            $query->on("shipments.ship_id", "=", "inventory.ship_id");
        })
        ->join('warehouses', function($query) {
            $query->on("warehouses.id", "=", "shipments.warehouse");
        })->where('warehouses.id', $request->ware_id)->get();


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
        $exportFilePath = 'Inventory/MonthlyReport.csv';// your file path, where u want to save
        if (!Storage::exists($exportFilePath)) {
            Storage::put($exportFilePath, '');
        }
        $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
        $writer->insertOne($headers);
        
        $csv_value = [];
        $count = 0;
        $writer->insertAll($records->toArray());
        return Storage::download($exportFilePath);
    }

    

    public function Monthly()
    {

        $ware_lists = Warehouse::get();

        //Monthly date //
        $date_array = [];
        $i = 0;
        while ($i < 31) {
            $today = Carbon::today();
            array_push($date_array, $today->subDays($i)->format('Y-m-d'));
            $i++;
        }
        /* Monthly closing count*/


        $Monthlyclose = Inventory::whereBetween('created_at', [Carbon::now()->subDays(30)->format('Y-m-d') . " 00:00:00", Carbon::now()->format('Y-m-d') . " 23:59:59"])
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



        $period = new DatePeriod(Carbon::now()->subDays(30), CarbonInterval::day(), Carbon::today()->endOfDay());

        $weeklyin = array_map(function ($datePeriod) use ($Monthlyclose) {
            $date = $datePeriod->format('Y-m-d');
            return $Monthlyclose->has($date) ? $Monthlyclose->get($date)->total : 0;
        }, iterator_to_array($period));
        $month_inv_count = array_reverse($weeklyin);

       
        /* Monthly closing Amount*/
        $month_close_amount = DB::connection('inventory')->table('inventory')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
        $shipment_closing = [];

        foreach ($month_close_amount as $key => $closingData) {

            foreach ($closingData as $items) {


                $shipment_closing[date('d-m-Y', strtotime($key))] = $items->quantity * $items->price;



                $period = new DatePeriod(Carbon::now()->subDays(30), CarbonInterval::day(), Carbon::today()->endOfDay());

                $weeklycloseamt = array_map(function ($datePeriod) use ($shipment_closing) {
                    $date = $datePeriod->format('d-m-Y');
                    return (isset($shipment_closing[$date])) ? $shipment_closing[$date] : 0;
                }, iterator_to_array($period));
              

                $month_closing_amt = array_reverse($weeklycloseamt);
            }
        }
     

        /* month Inwarding Amount*/
        $openShipmentData = DB::connection('inventory')->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(30))->get()->groupBy('created_at');

        $shipment_lists_date_wise = [];

        foreach ($openShipmentData as $key => $datewiseData) {

            foreach ($datewiseData as $items) {

                $item_lists = json_decode($items->items);

                foreach ($item_lists as $item) {

                    $shipment_lists_date_wise[date('d-m-Y', strtotime($key))] = $item->quantity * $item->price;

                    $period = new DatePeriod(Carbon::now()->subDays(30), CarbonInterval::day(), Carbon::today()->endOfDay());


                    $monthlyin = array_map(function ($datePeriod) use ($shipment_lists_date_wise) {
                        $date = $datePeriod->format('d-m-Y');
                        return (isset($shipment_lists_date_wise[$date])) ? $shipment_lists_date_wise[$date] : 0;
                    }, iterator_to_array($period));
                  


                    $month_inv_amt = array_reverse($monthlyin);
                }
            }
        }

     
        /* monthly Inwarding Count*/
        $openShipmentcount = DB::connection('inventory')->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(30))->get()->groupBy('created_at');

        $shipment_count_date_wise = [];

        foreach ($openShipmentcount as $key => $datewiseDataCount) {

            foreach ($datewiseDataCount as $items) {

                $item_list = json_decode($items->items);

                foreach ($item_list as $item) {

                    $shipment_count_date_wise[date('d-m-Y', strtotime($key))] = $item->quantity;
                    $period = new DatePeriod(Carbon::now()->subDays(30), CarbonInterval::day(), Carbon::today()->endOfDay());

                    $weeklyincount = array_map(function ($datePeriod) use ($shipment_count_date_wise) {
                        $date = $datePeriod->format('d-m-Y');
                        return (isset($shipment_count_date_wise[$date])) ? $shipment_count_date_wise[$date] : 0;
                    }, iterator_to_array($period));
                    $month_closing_count = array_reverse($weeklyincount);
                }
            }
        };

      

        /* monthly Outwarding  Amount*/
        $outShipmentData = DB::connection('inventory')->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(30))->get()->groupBy('created_at');

        $out_shipment_lists_date_wise = [];

        foreach ($outShipmentData as $key => $datewisecount) {

            foreach ($datewisecount as $items) {

                $item_list = json_decode($items->items);

                foreach ($item_list as $item) {

                    $out_shipment_lists_date_wise[date('d-m-Y', strtotime($key))] = $item->quantity * $item->price;

                    $period = new DatePeriod(Carbon::now()->subDays(30), CarbonInterval::day(), Carbon::today()->endOfDay());

                    $weeklyout = array_map(function ($datePeriod) use ($out_shipment_lists_date_wise) {
                        $date = $datePeriod->format('d-m-Y');
                        return (isset($out_shipment_lists_date_wise[$date])) ? $out_shipment_lists_date_wise[$date] : 0;
                    }, iterator_to_array($period));
                    $month_out_closing_amt = array_reverse($weeklyout);
                }
            }
        }
      
        /* monthly Outwarding  Count*/
        $outShipmentcount = DB::connection('inventory')->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(31))->get()->groupBy('created_at');

        $outshipment_count_date_wise = [];

        foreach ($outShipmentcount as $key => $outwiseDataCount) {

            foreach ($outwiseDataCount as $items) {

                $item_list = json_decode($items->items);

                foreach ($item_list as $item) {

                    $outshipment_count_date_wise[date('d-m-Y', strtotime($key))] = $item->quantity;

                    $period = new DatePeriod(Carbon::now()->subDays(30), CarbonInterval::day(), Carbon::today()->endOfDay());

                    $weeklyoutcount = array_map(function ($datePeriod) use ($outshipment_count_date_wise) {
                        $date = $datePeriod->format('d-m-Y');
                        return (isset($outshipment_count_date_wise[$date])) ? $outshipment_count_date_wise[$date] : 0;
                    }, iterator_to_array($period));
                    $month_out_count = array_reverse($weeklyoutcount);
                }
            }
        }

        $month_data = [];
        foreach ($date_array as $k => $val) {
            $month_data[] = [
                $val,
                $month_closing_count[$k],
                $month_closing_amt[$k],
                $month_inv_count[$k],
                $month_inv_amt[$k],
                $month_out_count[$k],
                $month_out_closing_amt[$k],
                $month_closing_count[$k],
                $month_closing_amt[$k]
            ];
        }
        return view('inventory.report.monthly', compact('ware_lists', 'month_data'));
    }

    public function eportinvmonthly(Request $request)
    {
        $records = []; //Data from database
        $records = Inventory::query()
        ->select('warehouses.name', 'inventory.ship_id', 'inventory.asin', 'inventory.item_name', 'inventory.price', 'inventory.quantity', 'inventory.created_at', 'inventory.bin')
        ->join('shipments', function($query) {
            $query->on("shipments.ship_id", "=", "inventory.ship_id");
        })
        ->join('warehouses', function($query) {
            $query->on("warehouses.id", "=", "shipments.warehouse");
        })->where('warehouses.id', $request->ware_id)->get();


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
        $exportFilePath = 'Inventory/MonthlyReport.csv';// your file path, where u want to save
        if (!Storage::exists($exportFilePath)) {
            Storage::put($exportFilePath, '');
        }
        $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
        $writer->insertOne($headers);
        
        $csv_value = [];
        $count = 0;
        $writer->insertAll($records->toArray());
        return Storage::download($exportFilePath);
    }

}
