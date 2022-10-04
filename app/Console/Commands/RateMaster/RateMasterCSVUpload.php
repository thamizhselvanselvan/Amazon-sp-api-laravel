<?php

namespace App\Console\Commands\RateMaster;

use config;
use RedBeanPHP\R;
use League\Csv\Reader;
use Illuminate\Console\Command;
use App\Models\Admin\Ratemaster;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RateMasterCSVUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:ratemaster-csv-upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload csv file';

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
        $path = 'RateMaster/export-rate.csv';
        $file = Storage::path($path);
        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);

        $csv_data = [];

        foreach ($csv as $data) {

            $csv_data[] = [
                'weight' => $data['Weight'],
                'base_rate' => $data['Base_rate'],
                'commission' => $data['Commission'],
                'lmd_cost' => $data['Lmd_cost'],
                'source_destination' => $data['Source_destination'],

            ];
        }

        Ratemaster::upsert($csv_data, 'unique_weight_source', ['weight', 'base_rate', 'commission', 'lmd_cost', 'source_destination']);
    }
}
