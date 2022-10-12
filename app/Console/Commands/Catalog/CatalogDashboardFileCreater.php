<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use App\Services\SP_API\API\CatalogDashboardService;

class CatalogDashboardFileCreater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog-dashboard-file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create json file for catalog dashboard';

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
        $catalogDashboard = new CatalogDashboardService();
        $catalogDashboard->catalogDashboard();
    }
}
