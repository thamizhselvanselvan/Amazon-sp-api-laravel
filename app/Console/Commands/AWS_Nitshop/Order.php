<?php

namespace App\Console\Commands\AWS_Nitshop;

use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use App\Services\AWS_Nitshop\Index;
use Illuminate\Support\Facades\Log;

class Order extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aws:nitshop:order';

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
        //Process Management start
        $process_manage = [
            'module'             => 'AWS',
            'description'        => 'AWS nitshop order',
            'command_name'       => 'aws:nitshop:order',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        $order = new Index;
        $order->order();

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }
}
