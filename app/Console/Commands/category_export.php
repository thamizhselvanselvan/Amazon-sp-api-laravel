<?php

namespace App\Console\Commands;

use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class category_export extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Mosh:Cat_Export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Categories of Asin';

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
        $csv_data_temp = ['asin', 'display_name', 'classification_id', 'tree'];

        if (!Storage::exists("Cliqnshop/category_import/category_asin.csv")) {
            return false;
        } else {

            $csv_data = CSV_Reader("Cliqnshop/category_import/category_asin.csv");
            foreach ($csv_data as $data) {
                $asin[] = $data['ASIN'];
            }
            // po($asin);
            // exit;
            // $count = count($asin);
            $result = DB::connection('catalog')->table('catalognewuss')
                ->whereIn('asin', $asin)->pluck('browse_classification', 'asin');

            $csv_data = [];
            $c_id = [];
            foreach ($result as $key => $value) {

                $data = json_decode($value, true);
                if(isset($data['classificationId']) && isset( $data['displayName']))
                {
                    $c_id[] = $data['classificationId'];
                    $csv_data[$data['classificationId']] = [
                        'asin' => $key,
                        'classificationId' => $data['classificationId'],
                        'd_name' => $data['displayName'],
                    ];
                }
                else
                {
                    $csv_data[$key]['asin']=$key;
                }
            }

            $treename = DB::connection('catalog')->table('categoriestree')
                ->whereIn('browseNodeId', $c_id)->pluck('Tree', 'browseNodeId')->toArray();

            foreach ($treename as $key => $results) {

                if (array_key_exists($key, $csv_data)) {
                    $csv_data[$key]['tree'] = str_replace(",","  >  ",str_replace(", "," ",$results));
                }
            }
           
            $headers =['Asin', 'Category Id','Category Name', 'Tree'];
            $exportFilePath = 'test/Categories.csv';
            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $writer->insertOne($headers);
            $writer->insertAll($csv_data);

           // return Storage::download($exportFilePath);
        }
    }
}
