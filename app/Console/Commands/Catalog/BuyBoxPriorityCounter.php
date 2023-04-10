<?php

namespace App\Console\Commands\Catalog;

use App\Models\Aws_credential;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class BuyBoxPriorityCounter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:buybox_priority_count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Counts Credentials In Use For Each Priority';

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
        $codes = ['in' => '11', 'us' => '4'];

        $counts = [];
        foreach ($codes as $key => $code) {
            $counts[$key] = Aws_credential::query()
                ->where(['mws_region_id' => $code, 'verified' => 1])
                ->selectRaw('count(case when credential_priority = "1" then 1 end) as "1", count(case when credential_priority = "2" then 1 end) as "2",
            count(case when credential_priority = "3" then 1 end) as "3",   count(case when credential_priority = "4" then 1 end) as "4"')
                ->first()->toArray();
        }
        // Cache::put('creds_count', $counts);
        cache()->rememberForever('creds_count', function () use ($counts) {
            return $counts;
        });
    }
}
