<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class deleteFailedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:delete_failed_jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete failed jobs of everything above 7 days';

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
        
        DB::table('failed_jobs')->where("failed_at", '<=', now()->subDays(7))->delete();
    }
}
