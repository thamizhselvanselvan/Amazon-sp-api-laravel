<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RenameAmazonInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:rename-amazon-invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $data = DB::connection('web')->select("SELECT * FROM amazoninvoice");
        foreach ($data as $details) {

            $awb_no = $details->awb;
            $booking_date = $details->booking_date;

            $year = date('Y', strtotime($booking_date));
            $month = date('F', strtotime($booking_date));


            $old_file = 'b2cship/' . $month . '_' . $year . '/' . $awb_no . '/Amazon-invoice.pdf';
            $new_file = 'b2cship/' . $month . '_' . $year . '/' . $awb_no . '/' . $awb_no . '_Invoice.pdf';

            $new_file_name = $awb_no . '_Invoice.pdf';
            // dd($do_path);
            if (Storage::disk('b2cship_do_space')->exists($old_file)) {

                Storage::disk('b2cship_do_space')->rename($old_file, $new_file);
                Log::info($old_file);
            }
            //
        }
    }
}
