<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use App\Services\AWS_Business_API\Search_Product_API\Search_Product;

class CliqnshopProductSearchRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:cliqnshop-product-search {searchKey}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search product from cliqnshop';

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
        //Process Management start
        // $process_manage = [
        //     'module'             => 'Cliqnshop Product Search',
        //     'description'        => 'Search product from cliqnshop',
        //     'command_name'       => 'mosh:cliqnshop-product-search',
        //     'command_start_time' => now(),
        // ];

        // $process_management_id = ProcessManagement::create($process_manage)->toArray();
        // $pm_id = $process_management_id['id'];

        $searchKey = $this->argument('searchKey');
        // $siteId = $this->argument('siteId');
        // $source = $this->argument('source');

        $ApiCall = new Search_Product();
        $result = $ApiCall->SearchProductByKey($searchKey);

        date_default_timezone_set('Asia/Kolkata');
        // $command_end_time = now();
        // ProcessManagementUpdate($pm_id, $command_end_time);
    }
}
