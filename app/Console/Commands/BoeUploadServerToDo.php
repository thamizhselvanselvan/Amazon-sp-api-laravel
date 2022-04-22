<?php

namespace App\Console\Commands;

use App\Models\BOE;
use Illuminate\Console\Command;
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
        $year = date('Y');
        $month = date('F');
        // dd($month);
        $files_path = BOE::where('do', 0)->limit(1)->get(['id','download_file_path']);
        // dd($files_path);
        // dd($boe[0]['download_file_path']);
        // exit;
        // BOE::where('do', 0)->chunk(5, function ($files_path) use($year, $month){



            foreach ($files_path as $file_path) {
                
                $file_path_array = explode('/', $file_path->download_file_path);
                $file_name = $file_path_array[count($file_path_array) - 1];
                $file = storage_path('app/' . $file_path->download_file_path);
                Storage::disk('do')->put($file_path->download_file_path, file_get_contents($file));
                BOE::where('id', $file_path->id)->update(['do' => 1]);
                
            }
            echo 'success';
        // });
    }
}
