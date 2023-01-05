<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use App\Services\Catalog\AllPriceExportCsvServices;

class CatalogAllPriceExportCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:ExportAllCatalogPrice {--columns=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export All Catalog Price Accroding To Country';

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

        $columns_data = $this->option('columns');
        $final_data = [];
        $explode_array = explode(',', $columns_data);

        foreach ($explode_array as $value) {
            list($key, $value) = explode('=', $value);
            $final_data[$key] = $value;
        }

        $fm_id = $final_data['fm_id'];
        $country_code = $final_data['destination'];

        $process_manage = [
            'module'             => 'Catalog_price_bb_in',
            'description'        => "Export $country_code Catalog's All Price",
            'command_name'       => 'mosh:ExportAllCatalogPrice',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        (new AllPriceExportCsvServices())->index($country_code, $fm_id);

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }
}
