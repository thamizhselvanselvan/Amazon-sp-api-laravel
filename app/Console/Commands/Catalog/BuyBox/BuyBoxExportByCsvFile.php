<?php

namespace App\Console\Commands\Catalog\BuyBox;

use League\Csv\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;

class BuyBoxExportByCsvFile extends Command
{
    private $countryCode;
    private $priority;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:buybox-export-by-csv-file {--columns=}';

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
        $columns_data = $this->option('columns');
        $final_data = [];
        $explode_array = explode(',', $columns_data);
        foreach ($explode_array as $value) {
            list($key, $value) = explode('=', $value);
            $final_data[$key] = $value;
        }
        $fm_id = $final_data['fm_id'];
        $this->countryCode = strtoupper($final_data['destination']);
        $this->priority = $final_data['priority'];
        $file_path = $final_data['path'];

        $select_column = [
            "asin",
            "delist",
            "available",
            "is_sold_by_amazon",
            "lowestprice_landedprice_amount",
            "lowestprice_listingprice_amount",
            "lowestprice_shipping_amount",
            "buybox_landedprice_amount",
            "buybox_listingprice_amount",
            "buybox_shipping_amount",
            "buybox_condition",
            "updated_at",
            "is_only_seller",
            "is_any_our_seller_own_bb",
            "next_highest_seller_price",
            "next_highest_seller_id",
            "next_lowest_seller_price",
            "next_lowest_seller_id",
            "bb_winner_price",
            "bb_winner_id"

        ];


        $csv = Reader::createFromPath(Storage::path($file_path), 'r');
        $records = $csv->setHeaderOffset(0);

        $asin = [];
        foreach ($records as $record) {
            $asin[] = $record['ASIN'];
        }
        $asin_chunk = array_chunk($asin, 5000);
        $filePath = 'excel/downloads/BuyBoxOfUploadedFile/' . $this->countryCode . '/' . $this->priority;
        $tableName = table_model_set(country_code: $this->countryCode, model: "bb_product_aa_custom_offer", table_name: "product_aa_custom_p" . $this->priority . "_" . strtolower($this->countryCode) . "_offer");
        if (!Storage::exists($filePath . '/' . 'asin.csv')) {
            Storage::put($filePath . '/' . 'asin.csv', '');
        }
        $csv_write = Writer::createFromPath(Storage::path($filePath . '/' . 'asin.csv'), 'w');
        $csv_write->insertOne($select_column);
        foreach ($asin_chunk as $key => $data) {

            $tableRecords = $tableName->select($select_column)
                ->whereIn('asin', $data)
                ->get()
                ->toArray();

            foreach ($tableRecords as $csv_data) {
                $csv_write->insertOne($csv_data);
            }
        }

        $zipPath = "excel/downloads/BuyBoxOfUploadedFile/" . $this->countryCode . '/' . $this->priority . '/zip/' . $this->countryCode . "BuyBoxAsin.zip";
        $totolFile[] = 'asin.csv';
        ZipFileConverter($zipPath, $totolFile, $filePath);

        $command_end_time = now();
        fileManagementUpdate($fm_id, $command_end_time);
    }
}
