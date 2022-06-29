<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use AWS\CRT\HTTP\Request;
use Illuminate\Console\Command;
use App\Models\inventory\Stocks;
use App\Models\Inventory\Shipment;
use Illuminate\Support\Facades\DB;
use App\Models\Inventory\Inventory;
use Illuminate\Support\Facades\Log;
use App\Models\Inventory\Outshipment;

class StocktTrack extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:stock-tracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Keeps Track Of Iventory closing Stocks ';

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
        /* Date */
        $date = Carbon::now()->format('d M Y');


        /* Opeaning Stock */
        $todayopeningstock = 0;
        $startTime = Carbon::today()->subDays(365);
        $endTimeYesterday = Carbon::yesterday()->endOfDay();
        $open = Inventory::whereBetween('created_at', [$startTime, $endTimeYesterday])->get();
        foreach ($open as  $data) {
            $todayopeningstock +=  $data['quantity'];
        }


        /* Opeaning Amount */
        $amt = [];
        $totalopenamt = 0;
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


        /* Inwarding count */
        $todayinward = 0;
        $dayin =   Shipment::whereDate('created_at',  Carbon::today()->toDateString())->get();

        foreach ($dayin as $key => $item) {

            $item_list = json_decode($item->items, true);

            foreach ($item_list as  $value) {
                $todayinward += $value['quantity'];
            }
        }
        /* Day Inwarding Amount */

        $dayinwamount = 0;
        $dayinamt =   shipment::whereDate('created_at',  Carbon::today()->toDateString())->get();
        foreach ($dayinamt as $key => $amtdayout) {

            $item_lists = json_decode($amtdayout->items);

            foreach ($item_lists as $item) {

                $dayinwamount += $item->quantity * $item->price;
            }
        }

        /* Outwarding count */
        $todayoutward = 0;
        $dayout =   Outshipment::whereDate('created_at',  Carbon::today()->toDateString())->get();
        foreach ($dayout as $key => $value) {
            $item_list = json_decode($value->items, true);

            foreach ($item_list as  $data) {

                $todayoutward += $data['quantity'];
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



        /* Closing Stock count */
        $todayclosingstock = 0;
        $close =   Inventory::get();

        foreach ($close as  $data) {
            $todayclosingstock +=  $data['quantity'];
        }



        /* Cloasing Amount */
        $closeamt =   Inventory::get();
        $closeprice = [];
        $dayclosing = [];
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

        DB::connection('inventory')->table('stocks')->insert([
            'date' => $date,
            'opeaning_stock' => $todayopeningstock,
            'opeaning_amount' => $totalopenamt,
            'inwarding' =>  $todayinward,
            'inw_amount' =>  $dayinwamount,
            'outwarding' =>   $todayoutward,
            'outw_amount' => $totaldayoutamt,
            'closing_stock' =>  $todayclosingstock,
            'closing_amount' =>   $dayclosingamt,
            'created_at' => now(),
        'updated_at' => now()
        ]);
    }
}
