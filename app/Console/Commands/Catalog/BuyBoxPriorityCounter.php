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

        $aws_creds = Aws_credential::query()
            ->select("aws_credentials.country_priority", "aws_credentials.mws_region_id", "aws_credentials.credential_priority")
            ->join('mws_regions as regions', function ($query) {
                $query->on('aws_credentials.mws_region_id', '=', 'regions.id');
            })
            ->where("auth_code", '!=', "Patch")
            ->where('verified',  1)
            ->get()->groupBy("credential_priority")->toArray();
        
        $results = [];
        foreach($aws_creds as $cred_priority =>  $aws_cred) {

            $aws_c = collect($aws_cred);
            
            $results["in"][$cred_priority - 1] = $aws_c->whereIn("country_priority", "IN")->count(); 
            $results["us"][$cred_priority - 1] = $aws_c->whereIn("country_priority", "US")->count(); 
      
        }     

        Cache::put('creds_count', $results);

        cache()->rememberForever('creds_count', function () use ($results) {
            return $results;
        });
    }
}
