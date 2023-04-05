<?php

namespace App\Console\Commands\Zoho;

use App\Services\Zoho\ZohoApi;
use Illuminate\Console\Command;

class GenerateAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:zoho:generate_token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will create access to token';

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
        
        (new ZohoApi(new_zoho: false))->generateAccessToken();
        (new ZohoApi(new_zoho: true))->generateNewAccessToken();

        return true;
    }
}
