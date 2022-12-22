<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\otherCatalog\OtherCatalogAsin;

class OtherCatalogAsinImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:other-catalog-asin-import {user} {type}';

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
        $user = $this->argument('user');
        $type = $this->argument('type');

        OtherCatalogAsin::where('user_id', $user)->where('source', $type)->delete();

        if ($type == 'com') {
            $path = 'OtherAmazon/amazomdotcom/Asin.txt';
        } else {
            $path = 'OtherAmazon/amazomdotin/Asin.txt';
        }


        $data = file_get_contents(Storage::path($path));
        $count = 0;
        $datas = preg_split('/[\r\n| |:|,]/', $data, -1, PREG_SPLIT_NO_EMPTY);
        $insert_data = [];

        foreach ($datas as $data) {

            $insert_data[] = [
                'user_id' => $user,
                'asin' => $data,
                'status' => 0,
                'source' => $type
            ];

            if ($count == 10000) {
                $count = 0;
                OtherCatalogAsin::insert($insert_data);

                $insert_data = [];
            }
            $count++;
        }
        OtherCatalogAsin::insert($insert_data);
    }
}
