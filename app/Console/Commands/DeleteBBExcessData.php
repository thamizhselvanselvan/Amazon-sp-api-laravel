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
            "us" => [1, 2, 3, 4],
            "in" => [1, 2, 3] 
        ];


        $this->info(endTime($start) . " JSON Read ");
        $tagger = 0;
        $cnt = 1;
        $total = 3000;

        $arrs = json_decode(Storage::get("asin_destination_uss.json"));
        $collecs = [];

        $this->info(endTime($start) . " JSON Read Finished");

        foreach($arrs as $arr) {

            $collecs[] = [
                "asin" => $arr->asin,
                "asin_exist_app360" => 1
            ];

            if($cnt == $total) {
                $cnt = 1;
                print($tagger ."\n");


                $this->info(endTime($start) . " Before Upsert");
    
                DB::connection('buybox')
                ->table("product_aa_custom_p1_us_offers")
                ->upsert($collecs, ["asin"], ["asin_exist_app360"]);
    
                $this->info(endTime($start) . " After Upsert");
             
                $collecs = [];
            }

            $cnt++;
        }

        $this->info(endTime($start) . " JSON Loop Finished");

        DB::connection('buybox')
                ->table("product_aa_custom_p1_us_offers")
                ->where("asin_exist_app360", 0)
                ->delete();
        

        $this->info("Finished");

    }

}
