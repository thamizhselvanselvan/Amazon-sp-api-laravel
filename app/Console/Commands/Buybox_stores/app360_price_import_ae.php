<?php

namespace App\Console\Commands\Buybox_stores;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\Buybox_stores\Products_ae;
use App\Models\Buybox_stores\Products_in;

class app360_price_import_ae extends Command
{
    private $base_percentage = 20;
    private $ceil_percentage = 30;
    private $price_calculate_type = "percent";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:app360:price_import_ae';

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
        $start_date = Carbon::now()->subMinutes(10);
        $end_date = Carbon::now()->subMinutes(5);

        $select_query = [

            // 'asin_destination_uss.priority',
            'pricing_aes.ae_price',
            'pricing_uss.usa_to_uae',
            'pricing_uss.available',
            'pricing_uss.asin',
            'pricing_aes.next_highest_seller_price',
            'pricing_aes.next_highest_seller_id',
            'pricing_aes.next_lowest_seller_price',
            'pricing_aes.next_lowest_seller_id',
            'pricing_aes.bb_winner_price',
            'pricing_aes.bb_winner_id',
            'pricing_aes.is_any_our_seller_won_bb',
        ];

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'asin_destination_');

        $data = $table_name->select($select_query)
            ->join('pricing_aes', 'asin_destination_uss.asin', '=', 'pricing_aes.asin')
            ->join('pricing_uss', 'asin_destination_uss.asin', '=', 'pricing_uss.asin')
            ->whereBetween("pricing_aes.updated_at", [$start_date, $end_date])
            ->get()->toArray();

        $insert_data_in = [];
        $asins = [];

        $total = 2000;
        $tagger = 0;
        $counter = 1;

        foreach ($data as $value) {

            $price_calculate = $this->calculate($value['usa_to_uae']);

            $insert_data_in[$tagger][] = [
                'asin' => $value['asin'],
                // 'priority' => $value['priority'],
                'availability' => $value['available'],
                'bb_price' => ceil((float)$value['ae_price']),
                'app_360_price' => ceil($value['usa_to_uae']),
                'base_price' => ceil($price_calculate['base_price']),
                'ceil_price' => ceil($price_calculate['ceil_price']),
                'lowest_seller_id' => $value['next_lowest_seller_id'],
                'lowest_seller_price' => ceil($value['next_lowest_seller_price']),
                'highest_seller_id' => $value['next_highest_seller_id'],
                'highest_seller_price' => ceil($value['next_highest_seller_price']),
                'bb_winner_id' => $value['bb_winner_id'],
                'bb_winner_price' => ceil($value['bb_winner_price']),
                'is_bb_own' => $value['is_any_our_seller_won_bb'],
                'cyclic' => 1
            ];

            $asins[$tagger][] = $value['asin'];

            if ($counter == $total) {
                $tagger++;
                $counter = 1;
            }

            $counter++;
        }

        $this->product_upsert($insert_data_in);
    }

    public function product_upsert($datas)
    {
        foreach ($datas as $data) {

            foreach ($data as $dat) {

                Products_ae::where("asin", $dat['asin'])->update($dat);
            }
        }
    }

    public function calculate($price)
    {
        if ($this->price_calculate_type == "percent") {

            $base_price  = removePercentage($price, $this->base_percentage);
            $ceil_price  = addPercentage($price, $this->ceil_percentage);

            return ['ceil_price' => $ceil_price, 'base_price' => $base_price];
        }
        
        $ceil_price = $price + $this->ceil_percentage;
        $base_price = $price - $this->base_percentage;

        return ['ceil_price' => $ceil_price, 'base_price' => $base_price];
    }
}
