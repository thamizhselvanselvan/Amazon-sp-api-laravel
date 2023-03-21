<?php

namespace App\Console\Commands\Catalog\Buybox;

use Carbon\Carbon;
use App\Models\FileManagement;
use Illuminate\Console\Command;

class BuyBoxAutoExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:buybox-auto-export-p1';

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
        $today = Carbon::today()->toDateTimeString();
        $now = Carbon::now()->toDateTimeString();

        $user_id = 'T1';
        $countryCode = ['1' => 'IN', '2' => 'US',  '3' => 'AE'];
        $key = 1;
        $file_info = [];

        QueryAgain:
        $data = FileManagement::where("module", "BUYBOX_EXPORT_$countryCode[$key]_1")
            ->whereBetween('created_at', [$today, $now])
            ->count();
        if ($data == '0') {

            $file_info = [
                "user_id"        => $user_id,
                "type"           => "BUYBOX_EXPORT",
                "module"         => "BUYBOX_EXPORT_$countryCode[$key]_1",
                "command_name"   => "mosh:buybox-export-asin"
            ];
            FileManagement::create($file_info);
            fileManagement();
        } else {

            $key++;

            if ($key > 3) {
                exit;
            }
            goto QueryAgain;
        }
    }
}
