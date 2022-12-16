<?php

namespace App\Console\Commands\Seller;

use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\seller\SellerAsinDetails;

class sellerAsinPriceExport extends Command
{
    private $fileNameOffset = 0;
    private $check;
    private $count = 1;
    private $writer;
    private $totalProductCount;
    private $currentCount;
    private $headers_default;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:seller-asin-price-export {--seller_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Seller Asin Price Details';

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
        $seller_id = $this->option('seller_id');

        $exportFilePath = "excel/downloads/seller/" . $seller_id . "/AmazonASINPricing";
        $deleteFilePath = "app/excel/downloads/seller/" . $seller_id;

        if (file_exists(storage_path($deleteFilePath))) {
            $path = storage_path($deleteFilePath);
            $files = (scandir($path));
            foreach ($files as $key => $file) {
                if ($key > 1) {
                    unlink($path . '/' . $file);
                }
            }
        }

        $record_per_csv = 1000000;
        $chunk = 100000;
        $headers = ['Asin', 'Is fulfilment By Amazon', 'Listing Price'];
        $this->check = $record_per_csv / $chunk;

        $this->totalProductCount = SellerAsinDetails::count();

        SellerAsinDetails::select(['asin', 'is_fulfilment_by_amazon', 'listingprice_amount'])
            ->where('seller_id', $seller_id)
            ->chunk($chunk, function ($records) use ($exportFilePath, $headers, $chunk) {

                if ($this->count == 1) {
                    if (!Storage::exists($exportFilePath . $this->fileNameOffset . '.csv.mosh')) {
                        Storage::put($exportFilePath . $this->fileNameOffset . '.csv.mosh', '');
                    }
                    $this->writer = Writer::createFromPath(Storage::path($exportFilePath . $this->fileNameOffset . '.csv.mosh'), "w");
                    $this->writer->insertOne($headers);
                }

                $records = $records->toArray();
                $records = array_map(function ($datas) {
                    return (array) $datas;
                }, $records);

                $this->writer->insertall($records);

                if ($this->check == $this->count) {
                    $this->fileNameOffset++;
                    $this->count = 1;
                } else {
                    ++$this->count;
                }
            });

        $path = "app/excel/downloads/seller/" . $seller_id;
        $path = storage_path($path);
        $files = (scandir($path));

        $filesArray = [];
        foreach ($files as $key => $file) {
            if ($key > 1) {
                if (str_contains($file, '.mosh')) {
                    $new_file_name = str_replace('.csv.mosh', '.csv', $file);
                    rename($path . '/' . $file, $path . '/' . $new_file_name);
                }
            }
        }
    }
}
