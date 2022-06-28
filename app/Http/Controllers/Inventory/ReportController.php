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

    public function  weekly()
    {
        $ware_lists = Warehouse::get();
        $week_data = $this->getweekly();
        return view('inventory.report.weekly', compact('ware_lists', 'week_data'));
    }
    public function getweekly()
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
        $openShipmentcount = DB::connection('inventory')->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

        $shipment_count_date_wise = [];

        foreach ($openShipmentcount as $key => $datewiseDataCount) {

            foreach ($datewiseDataCount as $items) {
                $item_list = json_decode($items->items);
        
                $days = date('d-m-Y', strtotime($key));

                if(array_key_exists($days, $shipment_count_date_wise)) {
                    $shipment_count_date_wise[$days] += count($item_list);
                } else {
                    $shipment_count_date_wise[$days] = count($item_list);
                }

            }
        }

        $week_inv_count = $this->dateTimeFilter(6, $shipment_count_date_wise);

        
        /* weekly Inwarding Amount*/
        $openShipmentData = DB::connection('inventory')->table('shipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

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

        $week_inv_amt  = $this->dateTimeFilter(6, $shipment_lists_date_wise);

        /* weekly Outwarding  Count*/
       
        $outShipmentcount = DB::connection('inventory')->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');

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

        $week_out_count = $this->dateTimeFilter(6, $out_count_date_wise);

    


        /* weekly Outwarding  Amount*/
        $outShipmentData = DB::connection('inventory')->table('outshipments')->where('created_at', '>=', Carbon::now()->subdays(7))->get()->groupBy('created_at');

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
    
        $week_out_amt  = $this->dateTimeFilter(6, $out_shipment_lists_date_wise);

        /* weekly closing count*/

        $week_close_cnt = DB::connection('inventory')->table('inventory')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
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

        $week_closing_count = $this->dateTimeFilter(6, $shipment_closing);


        


        /* weekly closing Amount*/
        $week_close_amount = DB::connection('inventory')->table('inventory')->where('created_at', '>=', Carbon::now()->subdays(6))->get()->groupBy('created_at');
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

        $week_closing_amt = $this->dateTimeFilter(6, $shipment_closing);


        $week_data = [];
        foreach ($date_array as $k => $val) {
            $week_data[] = [
                $val,
                $week_closing_count[$k],
                $week_closing_amt[$k],
                $week_inv_count[$k],
                $week_inv_amt[$k],
                $week_out_count[$k],
                $week_out_amt[$k],
                $week_closing_count[$k],
                $week_closing_amt[$k]
            ];
        }
       
        return $week_data;
    }

    public function dateTimeFilter($subDays = 6, $data) {

        $period = new DatePeriod(Carbon::now()->subDays($subDays), CarbonInterval::day(), Carbon::today()->endOfDay());

        return array_reverse(array_map(function ($datePeriod) use ($data) {
            $date = $datePeriod->format('d-m-Y');
            return (isset($data[$date])) ? $data[$date] : 0;
        }, iterator_to_array($period)));
    }

    public function eportinvweekly(Request $request)
    {
        $week_data = $this->getweekly();


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
        $writer->insertAll($week_data);
        return Storage::download($exportFilePath);
    }

    public function Monthly()
    {
        $ware_lists = Warehouse::get();
        $month_data = $this->getMonthly();
        return view('inventory.report.monthly', compact('ware_lists', 'month_data'));
    }

    public function getMonthly()
    {
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
        return $month_data;
    }

    public function eportinvmonthly(Request $request)
    {
        $month_data = $this->getMonthly();

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
        $writer->insertAll($month_data);
        return Storage::download($exportFilePath);
    }
}
