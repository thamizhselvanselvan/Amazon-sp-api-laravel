<?php

namespace App\Console\Commands\Buybox_stores;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use App\Models\Buybox_stores\Products_ae;
use App\Models\Buybox_stores\Product_availability_ae;

class Amazon_availability_push_ae extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:availability_push_ae';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Amazon Availability Push in AE Stores';

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
        $start_date = Carbon::now()->subMinutes(30);
        $end_date = Carbon::now()->subMinutes(1);

        $products = Products_ae::query()
            ->whereBetween("updated_at", [$start_date, $end_date])
            ->where("cyclic", 1)
            ->where("cyclic_availability", 0)
            ->limit(1000)
            ->get();

        if ($products->count() <= 0) {

            Products_ae::query()->update(['cyclic_availability' => 0]);
            return false;
        }

        foreach ($products as $product) {

            $availability = $product->availability;
            $current_availability = $product->current_availability;

            if($current_availability != $availability) {
                
                Product_availability_ae::insert(
                    [
                        "store_id" => $product->store_id,
                        "asin" => $product->asin,
                        "product_sku" => $product->product_sku,
                        "current_availability" => $current_availability,
                        "push_availability" => $availability,
                    ]
                );
            }

        }
    }
}
