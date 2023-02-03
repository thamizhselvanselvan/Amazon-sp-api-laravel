<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Catalog\PricingUs;
use App\Services\Catalog\PriceConversion;

class CatalogBuyBoxPriceRecalculate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog-buybox-price-recalculate {--columns=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-calculate the price of buybox';

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

        $priceConversion = new PriceConversion();
        $price_calculation_limit = getSystemSettingsValue('buybox_price_recalculation_limit', 1000);

        queryAgain:
        $records = PricingUs::query()
            ->select('asin', 'weight', 'us_price')
            ->where('status', '0')
            ->limit($price_calculation_limit)
            ->get()
            ->toArray();

        $updatingRecord = [];
        foreach ($records as $record) {

            $convertedPriceB2B = $priceConversion->USAToINDB2B($record['weight'], $record['us_price']);
            $convertedPriceB2C = $priceConversion->USAToINDB2C($record['weight'], $record['us_price']);

            $updatingRecord[] = [
                'asin' => $record['asin'],
                'status' => 1,
                'weight' => $record['weight'],
                'us_price' => $record['us_price'],
                'usa_to_in_b2b' => $convertedPriceB2B,
                'usa_to_in_b2c' => $convertedPriceB2C
            ];
        }
        PricingUs::upsert($updatingRecord, ['asin_unique'], ['asin', 'status', 'weight', 'us_price', 'usa_to_in_b2b', 'usa_to_in_b2c']);
        $data = PricingUs::where('status', 0)->get()->count('id');

        if ($data != 0) {
            goto queryAgain;
        }

        $command_end_time = now();
        fileManagementUpdate($fm_id, $command_end_time);
    }
}
