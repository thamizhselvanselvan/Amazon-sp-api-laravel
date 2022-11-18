<?php

namespace App\Console\Commands;

use League\Csv\Reader;
use App\Models\Invoice;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class InvoiceCsvImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:invoice-csv-import {--columns=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invoice csv file import';

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
        $column_data = $this->option('columns');

        $final_data = [];
        $explode_array = explode(',', $column_data);

        foreach ($explode_array as $key => $value) {
            list($key, $value) = explode('=', $value);
            $final_data[$key] = $value;
        }
        $file_management_id = $final_data['fm_id'];
        // po($file_management_id);
        $file_path = $final_data['path'];
        $count = 0;
        $upsert_columns = [
            'amazon_order_id',
            'invoice_no',
            'mode',
            'bag_no',
            'invoice_date',
            'sku',
            'channel',
            'shipped_by',
            'awb_no',
            'arn_no',
            'store_name',
            'store_add',
            'bill_to_name',
            'bill_to_add',
            'ship_to_name',
            'ship_to_add',
            'item_description',
            'hsn_code',
            'qty',
            'currency',
            'product_price',
            'taxable_value',
            'total_including_taxes',
            'grand_total',
            'no_of_pcs',
            'packing',
            'dimension',
            'actual_weight',
            'charged_weight',
            // 'sr_no',
            // 'client_code',
        ];

        try {

            $records = Reader::createFromPath(Storage::path($file_path), 'r');
            $records->setHeaderOffset(0);
            foreach ($records as $record) {

                $invoice_data[] = [
                    'amazon_order_id'         => htmlspecialchars(trim($record['Amazon_order_id'])),
                    'invoice_no'              => htmlspecialchars(trim($record['Invoice_no'])),
                    'mode'                    => $record['Mode'],
                    'bag_no'                  => $record['Bag_no'],
                    'invoice_date'            => $record['Invoice_date'],
                    'sku'                     => $record['Sku'],
                    'channel'                 => $record['Channel'],
                    'shipped_by'              => htmlspecialchars($record['Shipped_by']),
                    'awb_no'                  => $record['Awb_no'],
                    'arn_no'                  => $record['Arn_no'],
                    'store_name'              => htmlspecialchars($record['Store_name']),
                    'store_add'               => htmlspecialchars($record['Store_add']),
                    'bill_to_name'            => htmlspecialchars($record['Bill_to_name']),
                    'bill_to_add'             => htmlspecialchars($record['Bill_to_add']),
                    'ship_to_name'            => htmlspecialchars($record['Ship_to_name']),
                    'ship_to_add'             => htmlspecialchars($record['Ship_to_add']),
                    'item_description'        => htmlspecialchars($record['Item_description']),
                    'hsn_code'                => $record['Hsn_code'],
                    'qty'                     => $record['Qty'],
                    'currency'                => $record['Currency'],
                    'product_price'           => $record['Product_price'],
                    'taxable_value'           => $record['Taxable_value'],
                    'total_including_taxes'   => $record['Total_including_taxes'],
                    'grand_total'             => $record['Grand_total'],
                    'no_of_pcs'               => $record['No_of_pcs'],
                    'packing'                 => $record['Packing'],
                    'dimension'               => $record['Dimension'],
                    'actual_weight'           => $record['Actual_weight'],
                    'charged_weight'          => $record['Charged_weight'],
                ];

                if ($count == 20) {
                    Invoice::upsert($invoice_data, ['invoice_no_sku_unique'], $upsert_columns);
                    $count = 0;
                    $invoice_data = [];
                }
                $count++;
            }
        } catch (Exception $e) {

            $this->throwError($e, $file_management_id);
        }

        try {

            Invoice::upsert($invoice_data, ['invoice_no_sku_unique'], $upsert_columns);
            $command_end_time = now();
            fileManagementUpdate($file_management_id, $command_end_time);
        } catch (Exception $e) {

            $this->throwError($e, $file_management_id);
        }
    }

    public function throwError($e, $file_management_id)
    {
        $getMessage = $e->getMessage();
        $getCode = $e->getCode();
        $getFile = $e->getFile();
        $getLine = $e->getLine();

        $slackMessage = "Message: $getMessage
        Code: $getCode
        File: $getFile
        Line: $getLine";

        slack_notification('app360', 'Invoic Import', $slackMessage);
        $command_end_time = now();
        $status = '3'; //Failed
        fileManagementUpdate($file_management_id, $command_end_time, $status, $getMessage);
    }
}
