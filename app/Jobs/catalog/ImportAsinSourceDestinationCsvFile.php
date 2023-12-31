<?php

namespace App\Jobs\catalog;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Services\Catalog\CsvAsinImport;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ImportAsinSourceDestinationCsvFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $payload;
    public $timeout = 60 * 2;
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
        $chunk_data = $this->payload;
        $csv_import = new CsvAsinImport();

        if ($chunk_data['module'] == 'BuyBox') {

            $csv_import->ImportAsinIntoBuyBox($chunk_data);
        } else {

            $csv_import->AsinImport($chunk_data);
        }
    }
}
