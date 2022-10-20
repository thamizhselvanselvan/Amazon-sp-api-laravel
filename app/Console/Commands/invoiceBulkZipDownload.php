<?php

namespace App\Console\Commands;

use ZipArchive;
use League\Csv\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

class invoiceBulkZipDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:invoice-bulk-zip-download {passid} {currenturl} {mode} {invoice_date} {current_page_no} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download zip file for invoice';

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
        $passid = $this->argument('passid');
        $currenturl = $this->argument('currenturl');
        $mode = $this->argument('mode');
        $invoice_date = $this->argument('invoice_date');
        $current_page_no = $this->argument('current_page_no');

        // $path = 'invoice/zip/' . 'invoice.zip';
        // Storage::delete($path);
        // Log::warning("Invoice zip download excuted handle!");

        $excelid = explode('-', $passid);

        foreach ($excelid as $getId) {

            $id = DB::connection('web')->select("SELECT * from invoices where id ='$getId' ");

            foreach ($id as $key => $value) {

                $invoice_no = $value->invoice_no;
                $url = $currenturl . '/invoice/convert-pdf/' . $invoice_no;
                $path = "invoice/$mode/$invoice_date/invoice$invoice_no.pdf";

                if (!Storage::exists($path)) {
                    Storage::put($path, '');
                }

                $exportToPdf = storage::path($path);
                Browsershot::url($url)
                    ->setNodeBinary('D:\laragon\bin\nodejs\node.exe')
                    ->showBackground()
                    ->savePdf($exportToPdf);

                $saveAsPdf[] = 'invoice' . $invoice_no . '.pdf';
            }
        }

        $zip_path = 'invoice/';
        $zip = new ZipArchive;
        $zip_path = 'invoice/' . $mode . '/' . $invoice_date . '/' . 'zip/' . 'invoice' . $current_page_no . '.zip';

        $fileName = Storage::path($zip_path);

        if (!Storage::exists($zip_path)) {
            Storage::put($zip_path, '');
        } else {
            unlink(Storage::path($zip_path));
        }

        if ($zip->open($fileName, ZipArchive::CREATE) === TRUE) {
            foreach ($saveAsPdf as $key => $value) {
                $path = Storage::path("invoice/$mode/$invoice_date/$value");
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }
}
