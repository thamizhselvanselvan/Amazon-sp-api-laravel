<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteBBExcessData extends Command
{
    public $sources = ['IN', 'US'];
    private $gross;
    private $unavailable;
    private $bb_delist_count = [];
    private $bb_unavailable_count = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:delete:bb_excess_asins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $counter = 1;
        $arrs = [];
        $tagger = 0;
        $cnt = 1;
        $total = 3000;
        $start  = startTime();
        
        $arrays = [
            "p1_us" => "p2_us",
            "p2_us" => "p3_us",
            "p1_us" => "p3_us",
            "p1_in" => "p2_in",
            "p2_in" => "p3_in",
            "p1_in" => "p3_in",
        ];
        $this->info(endTime($start) . " Main Loop START ");
        foreach($arrays as $first_table => $second_table) {

            DB::connection('buybox')
                ->table("product_aa_custom_{$second_table}_offers")
                ->update(['asin_exist_app360' => 0]);

                $this->info(endTime($start) . " $second_table UPDATE TO ZERO COLUMN");

            DB::connection('buybox')
                ->table("product_aa_custom_{$first_table}_offers")
                ->select('id', 'asin')
                ->chunkById(3000, function($records) use ($second_table) {

                    DB::connection('buybox')
                        ->table("product_aa_custom_{$second_table}_offers")
                        ->whereIn('asin', $records->pluck('asin')->toArray())
                        ->update(["asin_exist_app360" => 1]);
                });

                $this->info(endTime($start) . " $first_table COMPLETED");

                $value = DB::connection('buybox')
                ->table("product_aa_custom_{$second_table}_offers")
                ->where('asin_exist_app360', 1)->count();

                DB::connection('buybox')
                ->table("product_aa_custom_{$second_table}_offers")
                ->where('asin_exist_app360', 1)
                ->delete();    


                $this->info(endTime($start) . " $second_table delete COMPLETED. $value");

        }

        $this->info(endTime($start) . " MAIN LOOP END ");

        return true;
    }

}
