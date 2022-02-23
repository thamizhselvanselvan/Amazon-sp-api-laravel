<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProductCatalogImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:catalog-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Product catalog import';

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
        echo 'success';
    }
}
