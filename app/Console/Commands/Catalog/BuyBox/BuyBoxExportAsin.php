<?php

namespace App\Console\Commands\Catalog\BuyBox;

use ZipArchive;
use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BuyBoxExportAsin extends Command
{
    private $countryCode;
    private $priority;
    private $csvExportPath;
    private $count = 1;
    private $offset = 1;
    private $chunked_count = 0;
    private $csv_headers = [];
    private $CSV_Writer;
    private $totalFile = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:buybox-export-asin {--columns=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export asin from BuyBox table according to priority';

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
        $record_per_csv = 1000000;
        $chunk = 5000;


        $select_column = [
            "id",
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
        $this->csv_headers = [
            "id",
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
        $this->chunked_count = $record_per_csv / $chunk;
        $this->csvExportPath = "excel/downloads/BuyBox/" . $this->countryCode . "/" . $this->priority . "/asin";
        $tableName = table_model_set(country_code: $this->countryCode, model: "bb_product_aa_custom_offer", table_name: "product_aa_custom_p" . $this->priority . "_" . strtolower($this->countryCode) . "_offer");
        $tableName->select($select_column)->chunkById($chunk, function ($tableData) {
            $records = $tableData->toArray();

            if ($this->count == 1) {

                if (!Storage::exists($this->csvExportPath . $this->offset . '.csv')) {
                    Storage::put($this->csvExportPath . $this->offset . '.csv', '');
                }

                $this->totalFile[] = "asin" . $this->offset . ".csv";
                $this->CSV_Writer = Writer::createFromPath(Storage::path($this->csvExportPath . $this->offset . ".csv"), "w");
                $this->CSV_Writer->insertOne($this->csv_headers);
            }
            foreach ($records as $record) {

                $this->CSV_Writer->insertOne($record);
            }

            if ($this->count == $this->chunked_count) {
                $this->offset++;
                $this->count = 1;
            } else {
                ++$this->count;
            }
        });

        $zipPath = "excel/downloads/BuyBox/" . $this->countryCode . '/' . $this->priority . '/zip/' . $this->countryCode . "BuyBoxAsin.zip";
        $filePath = "excel/downloads/BuyBox/" . $this->countryCode . "/" . $this->priority;
        ZipFileConverter($zipPath, $this->totalFile, $filePath);
        $command_end_time = now();
        fileManagementUpdate($fm_id, $command_end_time);
    }
}
