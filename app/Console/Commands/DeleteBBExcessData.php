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

        // foreach($arrays as $country_source => $array) {

        //     $asin_destination_model = table_model_create(country_code: $country_source, model: 'Asin_destination', table_name: 'asin_destination_');

        //     foreach($array as $priority) {

        //         $this->info($country_source . " " .$priority);

        //         $this->info(endTime($start) . " Chunk Start ");
        //         $asin_destination_model->select('id', 'asin')
        //         ->where('priority', $priority)
        //         ->chunkbyid(3000, function ($result) use ($priority, $country_source, $start) {
        //             $this->info(endTime($start) . " Chunk Inside ");

        //             $collecs = []; 
        //             foreach($result->toArray() as $arrs) {
        //                 $collecs[] = [
        //                     "asin" => $arrs['asin'],
        //                     "asin_exist_app360" => 1,
        //                     "import_type" => "UP"
        //                 ];
        //             }   

        //             $this->info(endTime($start) . " Chunk Outside Loop ");
        //             $this->info(endTime($start) . " Upsert Started ");

        //             DB::connection('buybox')
        //             ->table("product_aa_custom_p{$priority}_{$country_source}_offers")
        //             ->upsert($collecs, ["asin"], ["asin_exist_app360", "import_type"]);

        //             $this->info(endTime($start) . " Upsert Finished ");

        //         });


        //     }


        // }

 

        $this->info(endTime($start) . " JSON Read ");
        $tagger = 0;
        $cnt = 1;
        $total = 3000;

        $arrs = json_decode(Storage::get("asin_destination_uss.json"));
        $collecs = [];

        $this->info(endTime($start) . " JSON Read Finished");

        foreach($arrs as $arr) {

            $collecs[$tagger][] = [
                "asin" => $arr->asin,
                "asin_exist_app360" => 1
            ];
            if($cnt == $total) {
                $tagger++;
                $cnt = 1;
                print($tagger ."\n");
            }

            $cnt++;
        }

        $this->info(endTime($start) . " JSON Loop Finished");


        foreach($collecs as $collec) {

            $this->info(endTime($start) . " Before Upsert");

            DB::connection('buybox')
            ->table("product_aa_custom_p1_us_offers")
            ->upsert($collec, ["asin"], ["asin_exist_app360"]);

            $this->info(endTime($start) . " After Upsert");
        }

        $this->info("Finished");

    }

}
