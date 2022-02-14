<?php

namespace App\Jobs;

use App\Models\universalTextile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class universalTextileDataImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $datas;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->datas= $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        foreach($this->datas as $offset => $data){

            universalTextile::create($data);
        }


    }
}
