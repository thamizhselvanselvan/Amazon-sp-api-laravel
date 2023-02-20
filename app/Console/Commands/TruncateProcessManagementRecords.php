<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\FileManagement;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;

class TruncateProcessManagementRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:truncate-process-management-records';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate process management records from database at every 15 days';

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
        ProcessManagement::truncate();
        $process_manage = [
            'module'             => 'Delete',
            'description'        => 'Truncate process-management and delete file-management records from table.',
            'command_name'       => 'mosh:truncate-process-management-records',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        $id = [];
        $previous_date = Carbon::now()->subDays(30)->toDateTimeString();
        $file_management_ids = FileManagement::where('created_at', '<', $previous_date)->get()->toArray();
        foreach ($file_management_ids as $file_management_id) {
            $id[] = $file_management_id['id'];
        }
        // FileManagement::whereIn('id', $id)->delete();

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }
}
