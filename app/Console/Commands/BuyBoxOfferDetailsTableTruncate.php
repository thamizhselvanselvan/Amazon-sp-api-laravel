<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BuyBoxOfferDetailsTableTruncate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:buybox-offers-seller-details-table-truncate {source} {priority}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate AE offers and seller-details table from buybox';

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
        $source = $this->argument('source');
        $priority = $this->argument('priority');

        $productOffersTable = table_model_set(country_code: $source, model: "bb_product_aa_custom_offer", table_name: "product_aa_custom_p" . $priority . "_" . strtolower($source) . "_offer");
        $productOffersTable->truncate();

        $sellerDetailsTable = table_model_set(country_code: $source, model: "bb_product_aa_custom_seller_detail", table_name: "product_aa_custom_p" . $priority . "_" . strtolower($source) . "_seller_detail");
        $sellerDetailsTable->truncate();
    }
}
