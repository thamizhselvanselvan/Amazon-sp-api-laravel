<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class store_cat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Mosh:store_cat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store Categories';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $csv_data =  CSV_Reader('Cliqnshop/category_import/Categories.csv');
        
        $cnt = 1;
        $total = 5000;
        $results = [];

        foreach ($csv_data as $data) {

            $nodeid = $data['Node ID'];
            $nodename = $data['Node Name'];
            $pid = $data['Browse Path ID'];
            $tree = $data['Tree'];

            if($cnt == $total) {
                $cnt = 1;

                DB::connection('catalog')->table('categoriestree')->insert($results);

                $results = [];
            }

            $results[] = [
                "browseNodeId" => $nodeid,
                "browseNodeName" => $nodename,
                "browsePathId" => $pid,
                "Tree" => $tree,
                "created_at" => now(),
                "updated_at" => now()
            ];

           $cnt++;
        } // END of Foreach Loop

        DB::connection('catalog')->table('categoriestree')->insert($results);

    }

}


