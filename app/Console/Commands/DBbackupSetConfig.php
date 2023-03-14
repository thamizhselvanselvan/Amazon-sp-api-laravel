<?php

namespace App\Console\Commands;

use App\Models\Admin\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class DBbackupSetConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:db_backup_config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Will Set Databases to Exclude While DB Backup to config';

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
        $datas =  Backup::where("status", 1)->get(["connection", "table_name"])->groupBy("connection");

        foreach ($datas as $connection => $table_names) {

            $table_names = collect($table_names)->pluck("table_name");

            Config::set(
                "database.connections.{$connection}.dump.excludeTables",
                $table_names
            );
           // $value = Config::get("database.connections.{$connection}.dump.excludeTables");
        }
    }
}
