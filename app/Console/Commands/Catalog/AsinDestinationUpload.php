<?php

namespace App\Console\Commands\Catalog;

use League\Csv\Reader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Catalog\AsinDestination;
use Illuminate\Support\Facades\Storage;

class AsinDestinationUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:Asin-destination-upload {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asin Destination File Upload';

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
        $user_id = $this->argument('user_id');
        $path = 'AsinDestination/asin.csv';
        $asins = Reader::createFromPath(Storage::path($path), 'r');
        $asins->setHeaderOffset(0);
        
        $Asin_record = [];
        foreach($asins as $key => $asin)
        {

         $Asin_record [] = [

            'asin'  => $asin['ASIN'],
            'user_id'   => $user_id,
            'destination' => $asin['Destination'],
         ];

        }
        AsinDestination::upsert($Asin_record, ['user_asin_destination_unique'], ['destination']);
        // log::alert($Asin_record);
    }
}
