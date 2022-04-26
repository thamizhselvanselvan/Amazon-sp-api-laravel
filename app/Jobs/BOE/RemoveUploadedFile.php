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
        // $chunk = 10;
        // BOE::where('do', 0)->chunk($chunk, function ($files_path) {

        //     foreach ($files_path as $fp) {
        //         $file = storage_path('app/' . $fp->download_file_path);
        //         Storage::disk('do')->put($fp->download_file_path, file_get_contents($file));
        //         BOE::where('id', $fp->id)->update(['do' => 1]);
        //     }
        // });
    }
}
