<?php

namespace App\Console\Commands\Catalog\Buybox;

use App\Models\FileManagement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class AutoExportP1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:buybox_catalog_auto_export_p1';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command will export IN AE US priority 1 data daily';

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
        $countrys = ['IN', 'US', 'AE'];

        $user_id = 124;
        $priority = 1;
        foreach ($countrys as $country) {

            $file_info = [
                "user_id"        => $user_id,
                "type"           => "BUYBOX_EXPORT",
                "module"         => "BUYBOX_EXPORT_${country}_${priority}",
                "command_name"   => "mosh:buybox-export-asin"
            ];

            FileManagement::create($file_info);
            fileManagement();
        }
    }
}
