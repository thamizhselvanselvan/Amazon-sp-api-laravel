<?php

namespace App\Console\Commands;

use App\Models\BOE;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\BOE\RemoveUploadedFile;

class RemoveUploadedFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:remove-uploaded-boe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove uploaded file from server';

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
    {Log::alert("remove uploaded file from server command executed at ".now());
        $count = BOE::where('do', 0)->count();
        $chunk = 10;
        if ($count == 0) {
            BOE::where('do', 1)->chunk($chunk, function ($files_path) {

                foreach ($files_path as $fp) {

                    $path_array = (explode('/', $fp->download_file_path));
                    unset($path_array[count($path_array) - 1]);
                    $new_path = storage_path('app/' . implode("/", $path_array));
                    if (is_dir($new_path)) {
                        $dir_files = scandir($new_path);
                        foreach ($dir_files as $key => $file) {
                            if ($key > 1) {
                                unlink($new_path . '/' . $file);
                            }
                        }
                        rmdir($new_path);
                    }
                }
            });
        }
        // RemoveUploadedFile::dispatch();
    }
}
