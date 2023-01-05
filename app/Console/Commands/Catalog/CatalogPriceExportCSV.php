<?php

namespace App\Console\Commands\Catalog;

use ZipArchive;
use League\Csv\Writer;
use Illuminate\Console\Command;
use App\Models\Catalog\PricingAe;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingSa;
use App\Models\Catalog\PricingUs;
use Illuminate\Support\Facades\Log;
use App\Services\Catalog\PriceExport;
use Illuminate\Support\Facades\Storage;
use App\Services\Catalog\AllPriceExportCsvServices;

class CatalogPriceExportCSV extends Command
{
    private $fileNameOffset = 0;
    private $check;
    private $count = 1;
    private $writer;
    private $totalProductCount;
    private $currentCount;
    private $headers_default;
    private $totalFile = [];
    private $country_code;
    private $priority;
    private $date;
    private $headers = [
        'in' => [
            'destination.asin as Asin' =>  'Asin',
            'pricing_ins.available' => 'Available',
            'pricing_ins.weight' => 'Weight',
            'pricing_ins.in_price' => 'IND Price',
            'pricing_ins.ind_to_uae' => 'IND To UAE',
            'pricing_ins.ind_to_sg' => 'IND To SG',
            'pricing_ins.ind_to_sa' => 'IND To SA',
            'pricing_ins.updated_at' => 'Updated At'
        ],
        'us' => [
            'destination.asin as Asin' => 'Asin',
            'pricing_uss.available' => 'Available',
            'pricing_uss.weight' => 'Weight',
            'pricing_uss.us_price' => 'US Price',
            'pricing_uss.usa_to_in_b2b' => 'US to IND B2B',
            'pricing_uss.usa_to_in_b2c' => 'US to IND B2C',
            'pricing_uss.usa_to_uae' => 'US to UAE',
            'pricing_uss.usa_to_sg' => 'US to SG',
            'pricing_uss.updated_at' => 'Updated At',
        ],
        'ae' => [
            'destination.asin as Asin' => 'Asin',
            'pricing_aes.available' => 'Available',
            'pricing_aes.weight' => 'Weight',
            'pricing_aes.ae_price' => 'AE Price',
            'pricing_aes.updated_at' => 'Updated At',
        ],
        'sa' => [
            'destination.asin as Asin' => 'Asin',
            'pricing_sas.available' => 'Available',
            'pricing_sas.weight' => 'Weight',
            'pricing_sas.sa_price' => 'SA Price',
            'pricing_sas.updated_at' => 'Updated At',
        ]
    ];

    private $csv_headers = [
        'in' => [],
        'us' => [],
        'ae' => [],
        'sa' => []
    ];



    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    //protected $signature = 'mosh:catalog-price-export-csv {priority} {destination} {fm_id}';
    protected $signature = 'mosh:catalog-price-export-csv {--columns=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export catalog Price in CSV according to Country code';

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
        $this->country_code = strtolower($final_data['destination']);
        $this->priority = $final_data['priority'];

        // (new PriceExport())->index($this->country_code, $fm_id, $this->priority);

        (new AllPriceExportCsvServices())->index($this->country_code, $fm_id, $this->priority);
        return true;
    }
}
