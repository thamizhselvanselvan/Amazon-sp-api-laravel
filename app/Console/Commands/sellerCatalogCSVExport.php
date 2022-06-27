<?php

namespace App\Console\Commands;

use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class sellerCatalogCSVExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:seller-catalog-csv-export {user} {id}';

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
        $user_name = $this->argument('user');
        $id = $this->argument('id');

        $exportFilePath = "excel/downloads/seller/" . $user_name . "/catalog/catalog";
        $deleteFilePath = "app/excel/downloads/seller/" . $user_name.'/catalog';
        if (file_exists(storage_path($deleteFilePath))) {
            $path = storage_path($deleteFilePath);
            $files = (scandir($path));
            foreach ($files as $key => $file) {
                if ($key > 1) {
                    unlink($path . '/' . $file);
                }
            }
        }

        $column_details = DB::connection('catalog')->select('DESCRIBE catalog');
        $column_name = [];
        foreach ($column_details as $key => $column_value) {
            if ($column_value->Field != 'seller_id' && $column_value->Field != 'id')
                $column_name[] = $column_value->Field;
        }
        $count = DB::connection('catalog')->select("SELECT count(asin) as count from catalog where seller_id = $id");
        $total_count = ($count[0]->count);
        $current_chunk = 0;
        $record_per_csv = 1000000; //10 L
        $chunk = 100000; // 1 L
        $offset = 0;
        $count = 1;
        $fileNameOffset = 1;
        $user = '';
       
        $headers = [];
        $check = $record_per_csv / $chunk;

        while ($current_chunk <= $total_count) {

            $records = DB::connection('catalog')->select("SELECT *, NULL AS seller_id from catalog where seller_id = $id limit $offset, $chunk");

            if ($count == 1) {
                if (!Storage::exists($exportFilePath . $fileNameOffset . '.csv')) {
                    Storage::put($exportFilePath . $fileNameOffset . '.csv', '');
                }
                $this->writer = Writer::createFromPath(Storage::path($exportFilePath . $fileNameOffset . '.csv'), "w");
                $this->writer->insertOne($column_name);
            }

            $record = array_map(function ($datas) {
                $dat = [];
                foreach ($datas as $key => $data) {

                    if ($key != 'id' && $key != 'seller_id') {
                        $dat[] = $data;
                    }
                }
                return (array) $dat;
            }, $records);

            $this->writer->insertall((array)$record);

            if ($check == $count) {
                $fileNameOffset++;
                $count = 1;
            } else {
                ++$count;
            }

            //pusher part
            $offset += $chunk;
            $current_chunk = $offset;
        }
    }
}
