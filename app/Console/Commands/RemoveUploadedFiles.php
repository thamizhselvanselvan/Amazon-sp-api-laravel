<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\BOE;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\BOE\RemoveUploadedFile;
use Illuminate\Support\Facades\Storage;

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
    {
        // Log::alert("remove uploaded file from server command executed at ".now());
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
        // Remove all 2 days ago file form Asin destination, Asin source and invoiceCSV folder start.
        $back_file_date = Carbon::now()->subDays(2)->toDateString();
        $Asin_source_destination_files = ['AsinDestination', 'AsinSource', 'invoiceCSV'];
        foreach ($Asin_source_destination_files as $Asin_source_destination_file) {

            $files = Storage::allFiles("${Asin_source_destination_file}");
            foreach ($files as $file_name) {

                $FileTime = date("F d Y H:i:s.", filemtime(Storage::path("${file_name}")));
                $current_file_date = Carbon::parse($FileTime)->toDateString();

                if ($back_file_date == $current_file_date) {
                    unlink(Storage::path($file_name));
                    log::alert('All file delete successfully from AsinDestination Folder');
                }
            }
        }
        // Remove all 2 days ago file form Asin destination, Asin source and invoiceCSV folder end.
    }
}
