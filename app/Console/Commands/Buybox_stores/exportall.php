<?php

namespace App\Console\Commands\Buybox_stores;

use App\Models\Buybox_stores\Product;
use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class exportall extends Command
{

    private $offset = 0;
    private $count = 1;
    private $total;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:export_all_stores_asins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'command will export all stores asin from buybox_stors Product Table';

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
        Log::alert(' EXPORT Under DEVELOPMENT');
        po(' EXPORT Under DEVELOPMENT');
        exit;
        $total_csv = 10;
        $chunk = 10;
        $this->total = $total_csv / $chunk;

        $records = Product::query()
            ->select('store_id', 'asin', 'latency')
            ->chunk(2, function ($result) {

                $headers = [
                    'Store ID',
                    'ASIN',
                    'Latency',
                ];

                $exportFilePath = 'aws-products/exports/exportall' . $this->offset . ".csv";
                if (!Storage::exists($exportFilePath)) {
                    Storage::put($exportFilePath, '');
                }

                $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
                $writer->insertOne($headers);

                $writer->insertAll($result->toArray());


                if ($this->total == $this->count) {
                    ++$this->offset;
                    $this->count = 1;
                } else {
                    ++$this->count;
                }
            });
    }
}
