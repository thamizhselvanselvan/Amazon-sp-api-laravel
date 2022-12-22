<?php

namespace App\Console\Commands\Inventory;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use Illuminate\Support\Facades\Log;
use App\Models\Inventory\Shipment_Inward_Details;
use App\Models\Inventory\Shipment_Outward_Details;


class warehouse_stock_track extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:warehouse-track';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'keeps track of Warehouse Wise report ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //Process Management start
        $process_manage = [
            'module'             => 'Inventory',
            'description'        => 'Keeps track of warehouse report',
            'command_name'       => 'mosh:warehouse-track',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];
        // $pm_id = ProcessManagementCreate($process_manage['command_name']);
        //Process Management end

        $ware = Warehouse::get();
        foreach ($ware as $war) {

            $val = $war->id;
            $name = $war->name;
            $date = Carbon::now()->format('d M Y');

            /* Inwarding count */
            $todayinward = 0;
            $dayin =  Shipment_Inward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('warehouse_id', $val)
                ->get();
            foreach ($dayin as $key => $item) {

                $todayinward += $item['quantity'];
            }
            /* Outwarding count */
            $todayoutward = 0;
            $dayout =   Shipment_Outward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('warehouse_id', $val)
                ->get();
            foreach ($dayout as $key => $value) {

                $todayoutward += $value['quantity'];
            }
            /* Opeaning Stock */
            $todayopening = 0;
            $todayopeningstock = 0;
            $startTime = Carbon::today()->subDays(365);
            $endTimeYesterday = Carbon::yesterday()->endOfDay();

            $open = Shipment_Inward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('warehouse_id', $val)
                ->get();
            foreach ($open as  $data) {
                $todayopening +=  $data['quantity'];
            }
            $todayoutstock = 0;
            $close =  Shipment_Outward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('warehouse_id', $val)
                ->get();
            foreach ($close as  $cdata) {
                $todayoutstock +=  $cdata['quantity'];
            }

            $todayopeningstock =  $todayopening - $todayoutstock;

            $startTimecls = Carbon::today()->subDays(365);
            $endTime = Carbon::now();

            $todayclosingstock = 0;
            $close =  Inventory::whereBetween('created_at', [$startTimecls, $endTime])
                ->where('warehouse_id', $val)
                ->get();

            foreach ($close as  $data) {
                $todayclosingstock +=  $data['balance_quantity'];
            }

            /* Opeaning Amount */
            $amt = [];
            $singlepricein = [];
            $singlepriceout = [];
            $totalpricein = [];
            $totalopenamt = 0;
            $totalpriceout = [];
            $openamtamt =  Shipment_Inward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('warehouse_id', $val)
                ->get();
            foreach ($openamtamt as $amt) {
                $singlepricein[] = [
                    'price' => $amt['price'],
                    'qty' => $amt['quantity'],
                    'total' => $amt['price'] * $amt['quantity'],
                ];
            }
            $closeamt =  Shipment_Outward_Details::whereBetween('created_at', [$startTime, $endTimeYesterday])
                ->where('warehouse_id', $val)
                ->get();
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

            $dayinamt =   Shipment_Inward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('warehouse_id', $val)
                ->get();
            $totaldayinvamt = 0;

            foreach ($dayinamt as $key => $amtday) {

                $totaldayinvamt += $amtday->quantity * $amtday->price;
            }

            /* Outwarding Amount */

            $dayoutamt =   Shipment_Outward_Details::whereDate('created_at',  Carbon::today()->toDateString())
                ->where('warehouse_id', $val)
                ->get();
            $totaldayoutamt = 0;
            foreach ($dayoutamt as $key => $amtdayout) {


                $totaldayoutamt += $amtdayout->quantity * $amtdayout->price;
            }

            /* Cloasing Amount */
            $dayclosingamt = 0;
            $closeamt =   Inventory::query()
                ->where('warehouse_id', $val)
                ->get();
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




            DB::connection('inventory')->table('warehouse_stocks')->insert([
                'date' =>   $date,
                'Warehouse' => $val,
                'opeaning_stock' =>   $todayopeningstock,
                'opeaning_amount' =>  $totalopenamt,
                'inwarding' => $todayinward,
                'inw_amount' =>  $totaldayinvamt,
                'outwarding' =>    $todayoutward,
                'outw_amount' => $totaldayoutamt,
                'closing_stock' =>  $todayclosingstock,
                'closing_amount'  => $dayclosingamt,
                'created_at' => now(),
                'updated_at' => now()

            ]);
        }


        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }
}
