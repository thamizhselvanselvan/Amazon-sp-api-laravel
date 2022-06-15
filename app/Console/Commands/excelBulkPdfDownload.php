<?php

namespace App\Console\Commands;

use ZipArchive;
use App\Models\Invoice;
use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

class excelBulkPdfDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:excel-bulkpdf-download';

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
        Log::warning("Export zip command executed handle !");
        $totalid = Invoice::get();
        foreach($totalid as $total)
        {
            $id = $total->id;
            $invoice_no = $total->invoice_no;
            $currenturl =  URL::current();
            $url = str_replace('download-all', 'convert-pdf', $currenturl. '/'.$id);
            $path = storage::path('invoice/invoice'.$invoice_no);
            $exportToPdf = $path. '.pdf';
            Browsershot::url($url)
            //->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
            ->showBackground()
            ->savePdf($exportToPdf); 
        }
        // begin create zip folder for pdf

        $zip = new ZipArchive;
        $fileName = Storage::path('zip/'.'invoice.zip');
        if($zip->open($fileName, ZipArchive::CREATE) === TRUE)
        {
            $files = File::files(Storage::path('invoice'));
            foreach($files as $key => $value)
            {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }
        
        Log::warning(" Pdf Download Successfully ");

        // end create zip folder for pdf
    }
}
