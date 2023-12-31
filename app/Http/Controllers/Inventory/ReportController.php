<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use League\Csv\Writer;
use Illuminate\Http\Request;
use App\Models\Inventory\Tag;
use App\Models\Inventory\Stocks;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use Illuminate\Support\Facades\Log;
use App\Models\Inventory\TagStocks;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Services\Inventory\ReportWeekly;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Inventory\WarehouseStocks;
use App\Services\Inventory\ReportMonthly;
use App\Models\Inventory\Shipment_Inward_Details;
use App\Models\Inventory\Shipment_Outward_Details;

class ReportController extends Controller
{
    //daily Entire report view
    public function daily()
    {
        /* Wareouse */
        $ware_lists = Warehouse::get();
        $tags = Tag::get();

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
        $singlepricein = [];
        $openamtamt =  Shipment_Inward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])->get();
        foreach ($openamtamt as $amt) {
            $singlepricein[] = [
                'price' => $amt['price'],
                'qty' => $amt['quantity'],
                'total' => $amt['price'] * $amt['quantity'],
            ];
        }
        $singlepriceout = [];
        $closeamt =  Shipment_Outward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])->get();
        foreach ($closeamt as $amt) {
            $singlepriceout[] = [
                'price' => $amt['price'],
                'qty' => $amt['quantity'],
                'total' => $amt['price'] * $amt['quantity'],
            ];
        }
        $totalpricein = [];
        foreach ($singlepricein as $sum) {
            $totalpricein[] = $sum['total'];
        }
        $totalpriceout = [];
        foreach ($singlepriceout as $sumclose) {
            $totalpriceout[] = $sumclose['total'];
        }

        $totalinamt =  array_sum($totalpricein);
        $totalotamt = array_sum($totalpriceout);

        $totalopenamt =   $totalinamt  -  $totalotamt;

        /* Day Inwarding Amount */

        $totaldayinvamt = 0;
        $dayinamt =   Shipment_Inward_Details::whereDate('created_at',  Carbon::today()->toDateString())->get();

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

        $dayclosingamt = 0;
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
            "open_stock_amt" =>round($totalopenamt,2),
            "inwarded" => $todayinward ? $todayinward : 0,
            "tdy_inv_amt" =>round ($totaldayinvamt,2),
            "outwarded" => $todayoutward ? $todayoutward :0,
            "tdy_out_amt" =>round ($totaldayoutamt,2),
            "closing_stock" => $todayclosingstock,
            "closing_amt" => round($dayclosingamt,2)
        ];

        return view('inventory.report.daily', compact('ware_lists', 'data', 'tags'));
    }

    //daily Entire report export
    public function eportdaily()
    {
        $startTime = Carbon::today();


        $week_data =  Stocks::whereDate('created_at',  Carbon::today()->toDateString())
            ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
            ->orderBy('id', 'ASC')
            ->get()
            ->unique();


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
        $exportFilePath = 'Inventory/dailyReport.csv';
        if (!Storage::exists($exportFilePath)) {
            Storage::put($exportFilePath, '');
        }
        $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
        $writer->insertOne($headers);


        $writer->insertAll($week_data->toarray());
        return Storage::download($exportFilePath);
    }

    //daily report view warehouse wise
    public function warerepo(Request $request)
    {
        if ($request->ajax()) {
            /* Date */
            $date = Carbon::now()->format('d M Y');

            /* Inwarding count */
            $todayinward = 0;
            $dayin =  Shipment_Inward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('warehouse_id', $request->id)
                ->get();
            foreach ($dayin as $key => $item) {

                $todayinward += $item['quantity'];
            }

            /* Outwarding count */
            $todayoutward = 0;
            $dayout =   Shipment_Outward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('warehouse_id', $request->id)
                ->get();
            foreach ($dayout as $key => $value) {

                $todayoutward += $value['quantity'];
            }

            /* Opeaning Stock */
            $todayopening = 0;
            $startTime = Carbon::today()->subDays(365);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();

            $open = Shipment_Inward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('warehouse_id', $request->id)
                ->get();
            foreach ($open as  $data) {
                $todayopening +=  $data['quantity'];
            }
            $todayoutstock = 0;
            $close =  Shipment_Outward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('warehouse_id', $request->id)
                ->get();
            foreach ($close as  $cdata) {
                $todayoutstock +=  $cdata['quantity'];
            }

            $todayopeningstock =  $todayopening - $todayoutstock;

            /* Closing Stock count */
            $startTimecls = Carbon::today()->subDays(365);
            $endTime = Carbon::now();

            $todayclosingstock = 0;
            $close =  Inventory::whereBetween('created_at', [$startTimecls, $endTime])
                ->where('warehouse_id', $request->id)
                ->get();

            foreach ($close as  $data) {
                $todayclosingstock +=  $data['balance_quantity'];
            }

            /* Opeaning Amount */
            $amt = [];
            $singlepricein = [];
            $openamtamt =  Shipment_Inward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('warehouse_id', $request->id)
                ->get();
            foreach ($openamtamt as $amt) {
                $singlepricein[] = [
                    'price' => $amt['price'],
                    'qty' => $amt['quantity'],
                    'total' => $amt['price'] * $amt['quantity'],
                ];
            }
            $singlepriceout = [];
            $closeamt =  Shipment_Outward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('warehouse_id', $request->id)
                ->get();
            foreach ($closeamt as $amt) {
                $singlepriceout[] = [
                    'price' => $amt['price'],
                    'qty' => $amt['quantity'],
                    'total' => $amt['price'] * $amt['quantity'],
                ];
            }
            $totalpricein = [];
            foreach ($singlepricein as $sum) {
                $totalpricein[] = $sum['total'];
            }
            $totalpriceout = [];
            foreach ($singlepriceout as $sumclose) {
                $totalpriceout[] = $sumclose['total'];
            }

            $totalinamt =  array_sum($totalpricein);
            $totalotamt = array_sum($totalpriceout);

            $totalopenamt =   $totalinamt  -  $totalotamt;

            /* Day Inwarding Amount */

            $totaldayinvamt = 0;
            $dayinamt =   Shipment_Inward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('warehouse_id', $request->id)
                ->get();

            foreach ($dayinamt as $key => $amtday) {

                $totaldayinvamt += $amtday->quantity * $amtday->price;
            }

            /* Outwarding Amount */

            $dayoutamt =   Shipment_Outward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('warehouse_id', $request->id)
                ->get();
            $totaldayoutamt = 0;
            foreach ($dayoutamt as $key => $amtdayout) {


                $totaldayoutamt += $amtdayout->quantity * $amtdayout->price;
            }

            /* Cloasing Amount */
            $closeamt =   Inventory::where('warehouse_id', $request->id)->get();

            $dayclosingamt = 0;
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



            return response()->json($data);
        }
    }

    //daily tag  report view
    public function tagwise(Request $request)
    {
        if ($request->ajax()) {

            $date = Carbon::now()->format('d M Y');

            /* Inwarding count */
            $todayinward = 0;
            $dayin =  Shipment_Inward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('tag', $request->id)
                ->get();
            foreach ($dayin as $key => $item) {

                $todayinward += $item['quantity'];
            }

            /* Outwarding count */
            $todayoutward = 0;
            $dayout =   Shipment_Outward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('tag', $request->id)
                ->get();
            foreach ($dayout as $key => $value) {

                $todayoutward += $value['quantity'];
            }

            /* Opeaning Stock */
            $todayopening = 0;
            $startTime = Carbon::today()->subDays(365);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();

            $open = Shipment_Inward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('tag', $request->id)
                ->get();
            foreach ($open as  $data) {
                $todayopening +=  $data['quantity'];
            }
            $todayoutstock = 0;
            $close =  Shipment_Outward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('tag', $request->id)
                ->get();
            foreach ($close as  $cdata) {
                $todayoutstock +=  $cdata['quantity'];
            }

            $todayopeningstock =  $todayopening - $todayoutstock;

            /* Closing Stock count */
            $startTimecls = Carbon::today()->subDays(365);
            $endTime = Carbon::now();

            $todayclosingstock = 0;
            $close =  Inventory::whereBetween('created_at', [$startTimecls, $endTime])
                ->where('tag', $request->id)
                ->get();

            foreach ($close as  $data) {
                $todayclosingstock +=  $data['balance_quantity'];
            }

            /* Opeaning Amount */
            $amt = [];
            $singlepricein = [];
            $openamtamt =  Shipment_Inward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('tag', $request->id)
                ->get();
            foreach ($openamtamt as $amt) {
                $singlepricein[] = [
                    'price' => $amt['price'],
                    'qty' => $amt['quantity'],
                    'total' => $amt['price'] * $amt['quantity'],
                ];
            }
            $singlepriceout = [];
            $closeamt =  Shipment_Outward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('tag', $request->id)
                ->get();
            foreach ($closeamt as $amt) {
                $singlepriceout[] = [
                    'price' => $amt['price'],
                    'qty' => $amt['quantity'],
                    'total' => $amt['price'] * $amt['quantity'],
                ];
            }
            $totalpricein = [];
            foreach ($singlepricein as $sum) {
                $totalpricein[] = $sum['total'];
            }
            $totalpriceout = [];
            foreach ($singlepriceout as $sumclose) {
                $totalpriceout[] = $sumclose['total'];
            }

            $totalinamt =  array_sum($totalpricein);
            $totalotamt = array_sum($totalpriceout);

            $totalopenamt =   $totalinamt  -  $totalotamt;

            /* Day Inwarding Amount */

            $totaldayinvamt = 0;
            $dayinamt =   Shipment_Inward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('tag', $request->id)
                ->get();

            foreach ($dayinamt as $key => $amtday) {

                $totaldayinvamt += $amtday->quantity * $amtday->price;
            }

            /* Outwarding Amount */

            $dayoutamt =   Shipment_Outward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('tag', $request->id)
                ->get();
            $totaldayoutamt = 0;
            foreach ($dayoutamt as $key => $amtdayout) {


                $totaldayoutamt += $amtdayout->quantity * $amtdayout->price;
            }

            /* Cloasing Amount */
            $closeamt =   Inventory::where('tag', $request->id)->get();

            $dayclosingamt = 0;
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


            return response()->json($data);
        }
    }

    //weekly Entire report view
    public function index(Request $request)
    {
        $ware_lists = Warehouse::get();
        $tag_lists = Tag::get();
        if ($request->ajax()) {
            $startTime = Carbon::today()->subDays(7);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();
            $data = Stocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->orderBy('id', 'DESC')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()

                ->editColumn('opeaning_stock_amt', function ($data) {
                    return "&#8377 " . $data->opeaning_amount;
                })
                ->editColumn('inward_amt', function ($data) {
                    return "&#8377 " . $data->inw_amount;
                })

                ->editColumn('outward_amount', function ($data) {
                    return "&#8377 " . $data->outw_amount;
                })

                ->editColumn('cls_amount', function ($data) {
                    return "&#8377 " . $data->closing_amount;
                })
                ->rawColumns(['opeaning_stock_amt', 'inward_amt', 'outward_amount', 'cls_amount'])
                ->make(true);
        }


        return view('inventory.report.weekly', compact('ware_lists', 'tag_lists'));
    }

    //weekly Entire report export
    public function exportinvweekly()
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

    //weekly warehouse wise report view
    public function diaplayinvweekly(Request $request)
    {
        if ($request->ajax()) {
            $warehouse = $request->ware_id;
            $startTime = Carbon::today()->subDays(7);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();

            $week_data =  WarehouseStocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
                ->orderBy('id', 'DESC')
                ->where('Warehouse', $warehouse)
                ->get();

            return response()->json(['success' => 'Export has been done successfully', $week_data]);
        }
    }

    //weekly warehouse wise report export
    public function expinvweeklywarewise(Request $request)
    {
        if ($request->ajax()) {

            $warehouse = $request->ware_id;
            $startTime = Carbon::today()->subDays(7);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();

            $week_data =  WarehouseStocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
                ->orderBy('id', 'DESC')
                ->where('Warehouse', $warehouse)
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
            $exportFilePath = 'Inventory/weeklywareReport' . $warehouse . '.csv';
            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $writer->insertOne($headers);


            $writer->insertAll($week_data->toarray());
            // return Storage::download($exportFilePath);

            return response()->json(['success' => 'Export has been done successfully']);
        }
    }

    //weekly warehouse wise report download
    public function downexpwarewise($ware_id)
    {
        return Storage::download('/Inventory/weeklywareReport' . $ware_id . '.csv');
    }

    //weekly tag wise report view
    public function tagdisplay(Request $request)
    {
        if ($request->ajax()) {
            $tag = $request->tag_id;
            $startTime = Carbon::today()->subDays(7);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();

            $tag_data =  TagStocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
                ->orderBy('id', 'DESC')
                ->where('tag', $tag)
                ->get();

            return response()->json(['success' => 'tag details fetched successfully', $tag_data]);
        }
    }

    //weekly tag wise report export
    public function tagexprt(Request $request)
    {
        if ($request->ajax()) {

            $tag = $request->tag_id;
            $startTime = Carbon::today()->subDays(7);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();

            $tag_data =  TagStocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
                ->orderBy('id', 'DESC')
                ->where('tag', $tag)
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
            $exportFilePath = 'Inventory/weeklytagReport' . $tag . '.csv';
            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $writer->insertOne($headers);


            $writer->insertAll($tag_data->toarray());
            // return Storage::download($exportFilePath);

            return response()->json(['success' => 'Export has been done successfully']);
        }
    }

    //weekly tag wise report download
    public function downexptagwise($tag_id)
    {
        return Storage::download('/Inventory/weeklytagReport' . $tag_id . '.csv');
    }

    //monthly  report view
    public function monthlyview(Request $request)
    {
        $ware_lists = Warehouse::get();
        $tag_lists = Tag::get();
        if ($request->ajax()) {
            $startTime = Carbon::today()->subDays(31);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();
            $data = Stocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
            ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
            ->orderBy('id', 'DESC')
            ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('opeaning_stock_amt', function ($data) {
                    return "&#8377 " . $data->opeaning_amount;
                })
                ->editColumn('inward_amt', function ($data) {
                    return "&#8377 " . $data->inw_amount;
                })

                ->editColumn('outward_amount', function ($data) {
                    return "&#8377 " . $data->outw_amount;
                })

                ->editColumn('cls_amount', function ($data) {
                    return "&#8377 " . $data->closing_amount;
                })
                ->rawColumns(['opeaning_stock_amt', 'inward_amt', 'outward_amount', 'cls_amount'])
                ->make(true);
        }


        return view('inventory.report.monthly', compact('ware_lists', 'tag_lists'));
    }

    //monthly  report Export
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

    //monthly warehouse wise report view
    public function monthlywaredisp(Request $request)
    {
        if ($request->ajax()) {
            $warehouse = $request->ware_id;
            $startTime = Carbon::today()->subDays(31);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();

            $month_data =  WarehouseStocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
                ->orderBy('id', 'DESC')
                ->where('Warehouse', $warehouse)
                ->get();

            return response()->json(['success' => 'Export has been done successfully', $month_data]);
        }
    }

    //monthly warehouse wise report export
    public function monthlywareexpo(Request $request)
    {
        if ($request->ajax()) {

            $warehouse = $request->ware_id;
            $startTime = Carbon::today()->subDays(31);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();

            $week_data =  WarehouseStocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
                ->orderBy('id', 'DESC')
                ->where('Warehouse', $warehouse)
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
            $exportFilePath = 'Inventory/monthlywarehouseReport' . $warehouse . '.csv';
            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $writer->insertOne($headers);


            $writer->insertAll($week_data->toarray());


            return response()->json(['success' => ' Monthly Export has been done successfully']);
        }
    }

    //monthly warehouse wise report Download
    public function monthlywareexplocal($ware_id)
    {
        return Storage::download('/Inventory/monthlywarehouseReport' . $ware_id . '.csv');
    }

    //monthly tag wise report view
    public function monthtagrepdisp(Request $request)
    {
        if ($request->ajax()) {
            $tag = $request->tag_id;
            $startTime = Carbon::today()->subDays(31);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();

            $tag_data =  TagStocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
                ->orderBy('id', 'DESC')
                ->where('tag', $tag)
                ->get();

            return response()->json(['success' => 'tag details fetched successfully', $tag_data]);
        }
    }

    //monthly tah wise report export
    public function monthtagrepexport(Request $request)
    {
        if ($request->ajax()) {

            $tag = $request->tag_id;
            $startTime = Carbon::today()->subDays(31);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();

            $tag_data =  TagStocks::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->select('date', 'opeaning_stock', 'opeaning_amount', 'inwarding', 'inw_amount', 'outwarding', 'outw_amount', 'closing_stock', 'closing_amount')
                ->orderBy('id', 'DESC')
                ->where('tag', $tag)
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
            $exportFilePath = 'Inventory/monhlytagReport' . $tag . '.csv';
            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $writer->insertOne($headers);


            $writer->insertAll($tag_data->toarray());
            // return Storage::download($exportFilePath);

            return response()->json(['success' => 'Export has been done successfully']);
        }
    }

    //monthly warehouse wise report Download
    public function monthlytagexplocal($ware_id)
    {
        return Storage::download('/Inventory/monhlytagReport' . $ware_id . '.csv');
    }
}
