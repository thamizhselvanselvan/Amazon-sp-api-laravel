<?php

namespace App\Jobs\AmazonInvoice;

use App\Models\AmazonInvoice;
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
        $order_id = $this->payload['Order_id'];

        $path = 'AmazonInvoice/';
        $file = storage_path('app/' . $path . $order_id . '.pdf');
        $awbData = DB::connection('b2cship')
            ->select("SELECT AWBNo, RefNo, BookingDate FROM Packet
                    WHERE RefNo = '$order_id'
                ");

        foreach ($awbData as $key => $value) {

            $AwbNo = $value->AWBNo;
            $order_id = $value->RefNo;
            $date = $value->BookingDate;

            $amazon_invoice = [
                'awb' => $AwbNo,
                'amazon_order_id' => $order_id,
                'booking_date' => $date,
            ];

            AmazonInvoice::create($amazon_invoice);

            $year = date('Y', strtotime($date));
            $month = date('F', strtotime($date));

            $do_path = 'b2cship/' . $month . '_' . $year . '/' . $AwbNo . '/' . $AwbNo . '_Invoice.pdf';
            Storage::disk('b2cship_do_space')->put($do_path, file_get_contents($file));

            AmazonInvoice::where('awb', $AwbNo)
                ->update(['status' => '1']);
        }

        unlink($file);
    }
}
