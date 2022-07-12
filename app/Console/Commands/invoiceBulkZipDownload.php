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
    protected $signature = 'pms:invoice-bulk-zip-download {--data_array=*}';

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
        $data = $this->option('data_array');
        $passid = $data['passid'];
        $currenturl = $data['currenturl'];
        $invoice_date = $data['invoice_date'];
        $invoice_mode = $data['invoice_mode'];

           $path = 'invoice/zip/'.'invoice.zip';
            Storage::delete($path);
        Log::warning("Invoice zip download excuted handle!");
       
        $excelid = explode('-', $passid);

        foreach ($excelid as $getId) {
            // $id = Invoice::where('id', $getId)->get();
            $id = DB::connection('web')->select("SELECT * from invoices where id ='$getId' ");
            foreach ($id as $key => $value) {
                $invoice_no = $value->invoice_no;
                $url = $currenturl.'/invoice/convert-pdf/'.$invoice_no;
                $path = 'invoice/invoice' . $invoice_no . '.pdf';
                if (!Storage::exists($path)) {
                    Storage::put($path, '');
                }
                $exportToPdf = storage::path($path);
                Browsershot::url($url)
                    ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
                    ->showBackground()
                    ->savePdf($exportToPdf);

                $saveAsPdf [] = 'invoice' . $invoice_no . '.pdf'; 
            }
        }
      
        $zip_path = 'invoice/';
        $zip = new ZipArchive;
        $path = 'invoice/zip/'.'invoice.zip';
        $file_path = Storage::path($path);
        
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }
        
        if($zip->open($file_path, ZipArchive::CREATE) === TRUE)
        {
            foreach($saveAsPdf as $key => $value)
            {
                $path = Storage::path('invoice/'.$value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }
}
