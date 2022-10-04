<?php

namespace App\Console\Commands\Label;

use ZipArchive;
use App\Models\Label;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;

class labelBulkZipDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:label-bulk-zip-download {passid} {currenturl}';

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
        $passid = $this->argument('passid');
        $currenturl = $this->argument('currenturl');
        
        $path = 'label/zip/' . 'label.zip';
        Storage::delete($path);
        
        $excelid = explode('-', $passid);
        foreach ($excelid as $getId) {
        
            $id = Label::where('id', $getId)->get();
            // $id = DB::connection('web')->select("select * from labels where id = '$getId' ");
            foreach ($id as $key => $value) {
                $awb_no = $value->awb_no;
                $url = str_replace('select-download', 'pdf-template', $currenturl . '/' . $getId);

                $path = 'label/label' . $awb_no . '.pdf';
                if (!Storage::exists($path)) {
                    Storage::put($path, '');
                }
                $exportToPdf = storage::path($path);
                Browsershot::url($url)
                    // ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
                    ->paperSize(576, 384, 'px')
                    ->pages('1')
                    ->scale(1)
                    ->margins(0, 0, 0, 0)
                    ->savePdf($exportToPdf);

                $saveAsPdf[] = 'label' . $awb_no . '.pdf';
            }
        }

        $zip = new ZipArchive;
        $zip_path = 'label/zip/' . 'label.zip';
        $fileName = Storage::path('label/zip/' . 'label.zip');
        if (!Storage::exists($zip_path)) {
            Storage::put($zip_path, '');
        }
        if ($zip->open($fileName, ZipArchive::CREATE) === TRUE) {
            foreach ($saveAsPdf as $key => $value) {
                $path = Storage::path('label/' . $value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }
}
