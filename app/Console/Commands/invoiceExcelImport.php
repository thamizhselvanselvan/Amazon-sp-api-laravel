<?php

namespace App\Console\Commands;

use RedBeanPHP\R;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class invoiceExcelImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:invoice-excel-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export uploaded excel sheet';

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
        $path = 'invoiceExcel/invoice.xlsx';

        $file = Storage::path($path);
        $data = Excel::toArray([], $file);

        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

        $header = [];
        $result = [];
        $check = ['.', '(', ')'];
        foreach ($data[0][0] as $key => $value) {
            if ($value) {
                $testing = str_replace(' ', '_', trim($value));
                $header[$key] = str_replace($check, '', strtolower($testing));
            }
        }
        //  po($header);
        foreach ($data as $result) {
            foreach ($result as $key2 => $record) {
                if ($key2 != 0) {
                    
                    $invoice_number = $record[0];
                    $sku = $record[13];
                    $id = NULL;
                    $Totaldata = DB::connection('web')->select("SELECT id from invoices where invoice_no ='$invoice_number' AND sku = '$sku'");

                    if (isset($Totaldata[0])) {
                        $id = $Totaldata[0]->id;
                    }
                    if ($id == NULL) {
                        $invoice = R::dispense('invoices');
                        foreach ($record as $key3 => $value) {
                            $name = (isset($header[$key3])) ? $header[$key3] : null;
                            if ($name && $record[0] != '') {
                                if (isset($header[1]) && $key3 == 1) {
                                    $dateset = $header[1];
                                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($record[$key3])->format("Y-m-d");
                                    $invoice->$dateset = $date;
                                } elseif ($key3 != 1) {
                                    $invoice->$name = $value;
                                }

                                R::store($invoice);
                            }
                        }
                    } else {
                        $update = R::load('invoices', $id);
                        foreach ($record as $key3 => $value) {
                            $name = (isset($header[$key3])) ? $header[$key3] : null;
                            if ($name && $record[0] != '') {
                                if (isset($header[1]) && $key3 == 1) {
                                    $dateset = $header[1];
                                    $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($record[1])->format("Y-m-d");
                                    $update->$dateset = $date;
                                } elseif ($key3 != 1) {

                                    $update->$name = $value;
                                }
                                R::store($update);
                            }
                        }
                    }
                }
            }
        }
        exit;
    }
}
