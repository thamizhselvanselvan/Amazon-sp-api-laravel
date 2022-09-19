<?php

namespace App\Console\Commands\Orders;

use League\Csv\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LabelMissingAddressUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:order-address-missing-upload';

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
        Log::alert('command working');
        $path = 'label/missing_address.csv';
        $file = Storage::path($path);

        $csv = Reader::createFromPath($file, 'r');

        foreach ($csv as $key => $data) {

            if ($key > 0) {
                $Order = $data[0];
                $Name = $data[2];
                $AddressLine1 = $data[3];
                $AddressLine2 = $data[4];
                $City = $data[5];
                $County = $data[6];
                $CountryCode = $data[7];
                $Phone = $data[8];
                $AddressType = $data[9];

                $address_array = [
                    'Name' => $Name,
                    'AddressLine1' => $AddressLine1,
                    'AddressLine2' => $AddressLine2,
                    'City' => $City,
                    'County' => $County,
                    'CountryCode' => $CountryCode,
                    'Phone' => $Phone,
                    'AddressType' => $AddressType
                ];

                $address_json = (json_encode($address_array));
                DB::connection('order')->select("
                    UPDATE orderitemdetails SET shipping_address  = '$address_json' 
                    WHERE amazon_order_identifier = '$Order'
                ");
            }
        }
    }
}
