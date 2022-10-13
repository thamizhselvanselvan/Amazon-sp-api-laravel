<?php

namespace App\Console\Commands\Catalog;

use League\Csv\Reader;
use League\Csv\Statement;
use Illuminate\Console\Command;
use App\Models\Catalog\AsinSource;
use App\Services\BB\PushAsin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class asinBulkImport extends Command
{
    private $country;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:asin-import {user_id} {--source=} {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Asin from CSV';

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
        //source and seller id
        // $push_to_bb = new PushAsin();

        $user_id = $this->argument('user_id');
        $sources = explode(',', $this->option('source'));

        $path = $this->argument('path');
        $csv = Reader::createFromPath(Storage::path($path), 'r');
        $csv->setDelimiter(",");
        $csv->setHeaderOffset(0);

        $source = buyboxCountrycode();
        $asin = [];
        $count = 0;

        foreach ($sources as $key1 => $this->country) {

            foreach ($csv as $key => $record) {
                $country_code = strtoupper($this->country);
                $seller_id = $source[$country_code];
                $asin = $record['ASIN'];

                $asin_details[] = [
                    'asin' => $asin,
                    'user_id' => $user_id,
                ];

                if ($count == 999) {

                    $model_name = table_model_create(country_code: $this->country, model: 'Asin_source', table_name: 'asin_source_');
                    $model_name->upsert($asin_details, ['user_asin_unique'], ['asin']);
                    $count = 0;
                    $asin_details = [];
                }
                $count++;
            }
            $table_name = table_model_create(country_code: $this->country, model: 'Asin_source', table_name: 'asin_source_');
            $table_name->upsert($asin_details, ['user_asin_unique'], ['asin']);


            $country_code_up = strtoupper($this->country);
            $queue = 'catalog';

            if ($country_code_up == 'IN') {

                $queue = 'catalog_IN';
            }
            $class = 'catalog\AmazonCatalogImport';

            // $table_name->where('status', 0)->chunk(10, function ($asins) use ($class,  $queue) {

            //     $asin_source = [];
            //     foreach ($asins as $asin) {

            //         $asin_source[] = [
            //             'asin' => $asin->asin,
            //             'source' => $this->country,
            //             'seller_id' => $asin->user_id
            //         ];
            //     }
            //     jobDispatchFunc($class, $asin_source, $queue);
            // });
        }
    }
}
