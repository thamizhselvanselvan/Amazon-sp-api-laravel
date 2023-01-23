<?php

namespace App\Console\Commands\Zoho;

use Illuminate\Console\Command;
use App\Services\Zoho\ZohoOrder;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;

class Zoho extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:zoho:save {--amazon_order_id=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order details store it in Zoho CRM';

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
            'module'             => 'Zoho Update',
            'description'        => 'Order details store it in Zoho CRM',
            'command_name'       => 'mosh:zoho:save',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        //for ($i = 0; $i < 400; $i++) {

        $amazon_order_id = $this->option('amazon_order_id');
        $force_update = $this->option('force');

        $zoho_order = new ZohoOrder;
        $data = $zoho_order->index($amazon_order_id, $force_update);

        po($data);

        # code...
        //}

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);

        return true;
    }
}
