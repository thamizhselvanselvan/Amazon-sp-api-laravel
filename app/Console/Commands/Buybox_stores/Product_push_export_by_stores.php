<?php

namespace App\Console\Commands\Buybox_stores;

use App\Models\Aws_credential;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Buybox_stores\Seller_id_name;

class Product_push_export_by_stores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:bb:product_push:export {store_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Product Push as CSV file by stores';

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
        $store_id = $this->argument("store_id");

        $data = Aws_credential::with(['mws_region'])
            ->where('seller_id', $store_id)
            ->get();
        $region_code = $data['0']['mws_region']['region_code'];

        $table_name = 'products_push_ins';
        if ($region_code == 'AE') {
            $table_name = 'products_push_aes';
        } else if ($region_code == 'SA') {
            $table_name = 'products_push_sas';
        }

        $store_lists = aws_merchant_ids();

        $headers = [
            "ASIN", "SKU", "Availability", "Excel Price", "Store Price", "Push Price",
            "Base Price", "Ceil Price", "Lowest Seller", "Lowest Seller Price",
            "Highest Seller ", "Highest Seller Price", "BB", "BB Price", "i have BB", "Any of our seller own BB"
        ];

        $product_push_datas = DB::connection("buybox_stores")->table($table_name)
            ->where(['store_id' => $store_id, 'push_status' => 0])
            ->get()->toArray();

        $csv_collections = [];
        $asins = [];

        $counter = 1;
        $total = 2000;
        $tagger = 0;


        foreach ($product_push_datas as $product_push_data) {

            $lowest_seller_name = $this->getsellername($product_push_data->lowest_seller_id);
            $highest_seller_name = $this->getsellername($product_push_data->highest_seller_id);
            $bb_winner_name = $this->getsellername($product_push_data->bb_winner_id);

            $csv_collections[] = [
                "asin" => $product_push_data->asin,
                "sku" => $product_push_data->product_sku,
                "availability" => $product_push_data->availability,
                "excel_price" => $product_push_data->app_360_price,
                "store_price" => $product_push_data->current_store_price,
                "push_price" => $product_push_data->push_price,
                "base_price" => $product_push_data->base_price,
                "ceil_price" => $product_push_data->ceil_price,
                "lsi" => $lowest_seller_name,
                "lsp" => $product_push_data->lowest_seller_price,
                "hsi" => $highest_seller_name,
                "hsp" => $product_push_data->highest_seller_price,
                "bbi" => $bb_winner_name,
                "bbp" => $product_push_data->bb_winner_price,
                "i_have_bb" => $this->i_have_bb($store_lists, $store_id, $product_push_data->bb_winner_id),
                "is_bb" => $this->any_of_our_seller_own_bb($store_lists, $product_push_data->bb_winner_id),
            ];
            $asins[$tagger][$store_id][]  = $product_push_data->asin;


            if ($total == $counter) {
                $tagger++;
                $counter = 1;
            }

            $counter++;
        }

        foreach ($asins as $tagger => $values) {

            foreach ($values as $store_id => $asin) {

                DB::connection("buybox_stores")->table($table_name)
                    ->where('store_id', $store_id)
                    ->whereIn('asin', $asin)
                    ->update(["push_status" => 1]);
            }
        }

        $file_time = now()->format('Y-m-d-H-i-s');
        $file_name = "product_push_{$store_id}_export_{$file_time}.csv";

        CSV_w("public/product_push/" . $file_name, $csv_collections, $headers);
        $this->info("CSV Generation Finished");
    }

    public function i_have_bb(array $store_lists, string $store_id, string $bb_winner_id): bool
    {
        return $store_lists[$store_id] == $bb_winner_id;
    }

    public function any_of_our_seller_own_bb(array $store_lists, string $bb_winner_id): bool|array
    {

        if (in_array($bb_winner_id, $store_lists)) {
            return true;
        }

        return false;
    }
    public function getsellername($seller_id)
    {

        $seller_data = Seller_id_name::where('seller_store_id', $seller_id)->first();

        $seller_name = $seller_id;
        if ($seller_id == '0') {
            $seller_name = '';
        } elseif (isset($seller_data->seller_name)) {
            $seller_name = $seller_data->seller_name;
        }
        return $seller_name;
    }
}
