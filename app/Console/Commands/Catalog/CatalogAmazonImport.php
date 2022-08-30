<?php

namespace App\Console\Commands\Catalog;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CatalogAmazonImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog-amazon-import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Catalog Amazon Import Queue';

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
        // log::alert('command is working');
        $sources = ['IN', 'US'];
        foreach($sources as $source){
            $asin_source = [] ;
            $count = 0;
            $queue = 'catalog';
            $class =  'catalog\AmazonCatalogImport';
            $table_name = table_model_create(country_code: $source, model: 'Asin_source', table_name: 'asin_source_');
            $asins  = $table_name->where('status', 0)->limit(1000)->get();
            foreach($asins as $asin){
                if($count == 10){
                    
                    jobDispatchFunc($class, $asin_source, $queue);
                    $asin_source = [];
                    $count = 0;
                }
                $asin_source [] = [
                    'asin' => $asin['asin'],
                    'seller_id' => $asin['user_id'],
                    'source' => $source,
                ];
                $count++;
            }
            
            jobDispatchFunc($class, $asin_source, $queue);
            
        }
    }
}
