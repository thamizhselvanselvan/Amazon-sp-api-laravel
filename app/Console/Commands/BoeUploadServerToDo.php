<?php

namespace App\Console\Commands;

use App\Models\BOE;
use App\Jobs\BOE\UploadBoeToDO;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Storage;

class BoeUploadServerToDo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:boe-upload-Do';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload BOE file from server to DO';

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
            'module'             => 'BOE',
            'description'        => 'Upload BOE file from server to Do',
            'command_name'       => 'pms:boe-upload-Do',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        $path = '';
        if (App::environment(['Production', 'production'])) {

            $path = 'prod/';
        } elseif (App::environment(['Staging', 'staging'])) {
            $path = 'staging/';
        } else {

            $path = 'local/';
        }

        $chunk = 100;
        BOE::where('do', 0)->chunk($chunk, function ($files_path) use ($path) {

            foreach ($files_path as $fp) {
                $file = storage_path('app/' . $fp->download_file_path);

                if(Storage::exists($$file)) {

                    Storage::disk('do')->put($path . $fp->download_file_path, file_get_contents($file));
                    BOE::where('id', $fp->id)->update(['do' => 1]);
                
                }
            }
        });

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }
}
