<?php

namespace App\Jobs\GoogleTranslate;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Services\GoogleTranslateServices\ArabicToEnglish;

class GoogleTranslateArabicToEnglish implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $payload;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $Translate = $this->payload;
        $googleTranslateAPI = new ArabicToEnglish();
        $googleTranslateAPI->TranslateAPI($Translate);
    }
}
