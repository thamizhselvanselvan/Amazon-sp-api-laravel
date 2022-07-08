<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory\Catalog;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Jobs\InventoryCatalogImport;
use App\Jobs\InventoryCatalogImportJob;

class Import_inventory_catalog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:inventory_catalog_import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inventroy Catalog Import';

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
        Catalog::where('item_name', '')->chunk(10, function ($records) {

            if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
    
                InventoryCatalogImportJob::dispatch([
                    'data' => $records,
                ])->onConnection('redis')->onQueue('inventory');;
            } else {
    
                InventoryCatalogImportJob::dispatch(
                    [
                        'data' => $records,
                    ]
                );
            }
        });
    }
}
