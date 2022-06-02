<?php

namespace App\Console\Commands;

use App\Models\BOE;
use App\Jobs\BOE\UploadBoeToDO;
use Illuminate\Console\Command;
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
        $path ='';
        if (App::environment(['Production', 'production'])) {

            $path = 'prod/';
        }
        elseif(App::environment(['Staging', 'staging']))
        {
            $path ='staging/';
        }
        else{

            $path ='local/';
        }

        $chunk = 100;
        BOE::where('do', 0)->chunk($chunk, function ($files_path) use($path) {

            foreach ($files_path as $fp) {
                $file = storage_path('app/' . $fp->download_file_path);
                Storage::disk('do')->put($path.$fp->download_file_path, file_get_contents($file));
                BOE::where('id', $fp->id)->update(['do' => 1]);
            }
        });

        Log::alert('Boe Uploaded to Do');
    //    UploadBoeToDO::dispatch();

    }
}
