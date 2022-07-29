<?php

namespace App\Jobs\AmazonInvoice;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class AmazonInvoiceUploadDO implements ShouldQueue
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
        $AwbNo = $this->payload['AwbNo'];
        $order_id = $this->payload['Order_id'];
        $date = $this->payload['Date'];

        $path = 'AmazonInvoice/';
        $file = storage_path('app/' . $path . $order_id . '.pdf');

        $year = date('Y', strtotime($date));
        $month = date('F', strtotime($date));

        $do_path = 'b2cship/' . $month . '_' . $year . '/' . $AwbNo . '/' . $AwbNo . '_Invoice.pdf';
        Storage::disk('b2cship_do_space')->put($do_path, file_get_contents($file));

        DB::connection('web')->update("UPDATE amazoninvoice SET status = '1' WHERE amazon_order_identifier = '$order_id' ");
        unlink($file);
    }
}
