<?php

namespace App\Jobs\AmazonInvoice;

use RedBeanPHP\R;
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
        // $AwbNo = $this->payload['AwbNo'];
        // $date = $this->payload['Date'];
        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');

        if (!R::testConnection('web', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password)) {
            R::addDatabase('web', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
            R::selectDatabase('web');
        }

        $order_id = $this->payload['Order_id'];

        $path = 'AmazonInvoice/';
        $file = storage_path('app/' . $path . $order_id . '.pdf');
        $awbData = DB::connection('b2cship')
            ->select("SELECT AWBNo, RefNo, BookingDate FROM Packet
                    WHERE RefNo = '$order_id'
                ");

        foreach ($awbData as $key => $value) {

            $job_data = [];
            $AwbNo = $value->AWBNo;
            $order_id = $value->RefNo;
            $date = $value->BookingDate;

            $amazon_invoice = R::dispense('amazoninvoice');
            $amazon_invoice->awb = $AwbNo;
            $amazon_invoice->amazon_order_identifier = $order_id;
            $amazon_invoice->booking_date = $date;
            $amazon_invoice->status  = '0';
            $amazon_invoice->created_at = now();

            R::store($amazon_invoice);
            $year = date('Y', strtotime($date));
            $month = date('F', strtotime($date));

            $do_path = 'b2cship/' . $month . '_' . $year . '/' . $AwbNo . '/' . $AwbNo . '_Invoice.pdf';
            Storage::disk('b2cship_do_space')->put($do_path, file_get_contents($file));

            DB::connection('web')->update("UPDATE amazoninvoice SET status = '1' WHERE awb = '$AwbNo' ");
        }

        unlink($file);
    }
}
