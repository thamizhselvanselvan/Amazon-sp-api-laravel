<?php

namespace App\Jobs\BOE;

use App\Models\BOE;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class RemoveUploadedFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $timeout = 500;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
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
    }
}
