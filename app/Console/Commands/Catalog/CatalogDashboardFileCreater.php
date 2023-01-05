<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use App\Models\ProcessManagement;
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
        $process_manage = [
            'module'             => 'Catalog Dashboard',
            'description'        => 'Catalog Dashboard Create',
            'command_name'       => 'mosh:catalog-dashboard-file',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        $catalogDashboard = new CatalogDashboardService();
        $catalogDashboard->catalogDashboard();

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }
}
