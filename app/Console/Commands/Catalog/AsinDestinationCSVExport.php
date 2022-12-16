<?php

namespace App\Console\Commands\Catalog;

use ZipArchive;
use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Catalog\AsinDestination;
use Illuminate\Support\Facades\Storage;

class AsinDestinationCSVExport extends Command
{
    private $offset = 0;
    private $count = 1;
    private $mode;
    private $writer;
    private $file_path;
    private $Total = [];
    protected $Files = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:asin-destination-csv-export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export asin-destination form DB to CSV file';

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
        $total_csv = 1000000;
        $chunk = 100000;
        $this->mode = $total_csv / $chunk;

        AsinDestination::orderBy('id')->chunk($chunk, function ($records) {

            if ($this->count == 1) {

                $this->file_path = "excel/downloads/asin_destination/asinDestinationExport" . $this->offset . ".csv";
                $this->Files[] = 'asinDestinationExport' . $this->offset . '.csv';
                if (!Storage::exists($this->file_path)) {
                    Storage::put($this->file_path, '');
                }
                $this->writer = Writer::createFromPath(Storage::path($this->file_path), "w");
                $header = ['Asin', 'Destination'];
                $this->writer->insertOne($header);
            }
            foreach ($records as $record) {
                $this->Total[] = [
                    'Asin' => $record['asin'],
                    'Destination' => $record['destination'],
                ];
            }

            $records = $records->toArray();
            $this->writer->insertall($this->Total);

            if ($this->mode == $this->count) {
                $this->offset++;
                $this->count = 1;
            } else {
                $this->count++;
            }
        });

        $zip = new ZipArchive;
        $path = 'excel/downloads/asin_destination/zip/CatalogAsinDestination.zip';
        $file_path = Storage::path($path);

        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }

        if ($zip->open($file_path, ZipArchive::CREATE) === TRUE) {
            foreach ($this->Files as $key => $value) {
                $path = Storage::path('excel/downloads/asin_destination/' . $value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }
}
