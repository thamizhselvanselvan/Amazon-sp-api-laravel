<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:test-command';

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
        $chunk = 10000;
        $total =  DB::connection('catalog')->select("SELECT cat.asin 
    FROM asin_source_ins as source 
    RIGHT JOIN catalognewins as cat 
    ON cat.asin=source.asin 
    WHERE source.asin IS NULL
   ");

        $loop = ceil(count($total) / $chunk);
        for ($i = 0; $i < $loop; $i++) {

            $data =  DB::connection('catalog')->select("SELECT cat.asin 
        FROM asin_source_ins as source 
        RIGHT JOIN catalognewins as cat 
        ON cat.asin=source.asin 
        WHERE source.asin IS NULL
        LIMIT 10000");
            $asin = [];
            foreach ($data as $record) {
                $asin[] = [
                    'asin' => $record->asin,
                    'user_id' => '13',
                    'status' => 0
                ];
            }
            $table = table_model_create(country_code: 'in', model: 'Asin_source', table_name: 'asin_source_');
            $table->upsert($asin, ['user_asin_unique'], ['asin', 'status']);
            Log::warning($asin);
            Log::warning('successfully' . $i);
        }
    }
}
