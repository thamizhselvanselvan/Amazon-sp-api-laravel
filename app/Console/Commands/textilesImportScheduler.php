<?php

namespace App\Console\Commands;

use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Console\Command;
use App\Models\Universal_textile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class textilesImportScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:textiles-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Universal Textiles Import';

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
        $url = 'https://files.channable.com/f8k02iylfY7c5YTsxH-SxQ==.csv';

        $source = file_get_contents($url);
        $path = 'universalTextilesImport/textiles.csv';

        Storage::put($path, $source);

        $csv = Reader::createFromPath(Storage::path($path), 'r');

        $csv->setDelimiter("\t");
        $csv->setHeaderOffset(0);


        $stmt = (new Statement())
            ->where(function (array $record) {
                return $record;
            })
            ->offset(0);
        // ->limit(2000);

        $records = $stmt->process($csv);

        $textiles = [];

        $count = 0;
        $tagger = 0;
        foreach ($records as $key => $record) {
            if (isset($record['id'])) {

                $record['textile_id'] = $record['id'];
                unset($record['id']);
            }

            $textiles[] = $record;
            if ($count == 1000) {

                Universal_textile::upsert($textiles, ['textile_id'], ['ean', 'brand', 'title', 'size', 'color', 'transfer_price', 'shipping_weight', 'product_type']);

                $count = 0;
                $textiles = [];
            }
            $count++;
        }

        Universal_textile::upsert($textiles, ['textile_id'], ['ean', 'brand', 'title', 'size', 'color', 'transfer_price', 'shipping_weight', 'product_type']);
    }
}
