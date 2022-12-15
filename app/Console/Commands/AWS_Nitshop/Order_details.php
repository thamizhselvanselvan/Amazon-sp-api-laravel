<?php

namespace App\Console\Commands\AWS_Nitshop;

use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use App\Services\AWS_Nitshop\Index;
use Illuminate\Support\Facades\Log;

class Order_details extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aws:nitshop:order_details';

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
            'module'             => 'AWS_nitshop_order_details',
            'description'        => 'AWS nitshop order item details',
            'command_name'       => 'aws:nitshop:order_details',
            'command_start_time' => now(),
        ];

        ProcessManagement::create($process_manage);
        $pm_id = ProcessManagementCreate($process_manage['command_name']);
        //Process Management end

        $order = new Index;
        $order->order_details();

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
        Log::notice($pm_id . '=> aws:nitshop:order_details');
    }
}
