<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MicroStatusReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:b2cship-microstatus-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Micro Status Report to create json file to store last 30 days micro status date';

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
        //Process Management start
        $process_manage = [
            'module'             => 'B2CShip',
            'description'        => 'Create json file to store last 30 days micro status data',
            'command_name'       => 'pms:b2cship-microstatus-report',
            'command_start_time' => now(),
        ];

        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];
        // $pm_id = ProcessManagementCreate($process_manage['command_name']);
        //Process Management end

        // Log::alert("microstatus report command executed at ".now());
        $today_sd = Carbon::today();
        $today_ed = Carbon::now();

        $yesterday_sd = Carbon::yesterday();
        $yesterday_ed = $yesterday_sd->toDateString();
        $yesterday_ed = $yesterday_ed . ' 23:59:59';

        $last7day_sd = Carbon::today()->subDays(7);
        $last7day_ed = $yesterday_ed;

        $last30day_sd = Carbon::today()->subDays(30);
        $last30day_ed = $yesterday_ed;
        $file_path = "MicroStatusJson/microstatus_details.json";

        $micro_status_mapping = DB::connection('b2cship')->select("SELECT DISTINCT  MicroStatusCode, Status, MicroStatusName FROM MicroStatusMapping");
        $micro_status_name = [];
        foreach ($micro_status_mapping as $micro_status_value) {
            $micro_status_name[$micro_status_value->MicroStatusCode] = $micro_status_value->MicroStatusName;

            $micro_status[$micro_status_value->Status] = $micro_status_value->MicroStatusName;
        }
        $packet_status = DB::connection('b2cship')->select("SELECT DISTINCT
            AwbNo, StatusDetails, CreatedDate 
            FROM PODTrans 
            WHERE CreatedDate BETWEEN '$last30day_sd' AND '$yesterday_ed'
            ORDER BY CreatedDate DESC
            ");

        $packet_status_details = collect($packet_status);

        $packet_status_yesterday = $this->packet_status($packet_status_details, $yesterday_sd, $yesterday_ed);
        $packet_status_7_day = $this->packet_status($packet_status_details, $last7day_sd, $last7day_ed);
        $packet_status_30_days = $this->packet_status($packet_status_details, $last30day_sd, $last30day_ed);

        $status_count_yesterday = $this->micro_status_count($micro_status, $packet_status_yesterday);
        $status_count_last_7day = $this->micro_status_count($micro_status, $packet_status_7_day);
        $status_count_last_30day = $this->micro_status_count($micro_status, $packet_status_30_days);

        //Json file addd
        $micros_status_data['yesterday'] = $status_count_yesterday;
        $micros_status_data['last7days'] = $status_count_last_7day;
        $micros_status_data['last30days'] = $status_count_last_30day;

        if (!Storage::exists($file_path)) {
            Storage::put($file_path, '');
        }
        File::put(Storage::path($file_path), json_encode($micros_status_data));


        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
        Log::notice($pm_id . '=> pms:b2cship-microstatus-report');
    }

    public function packet_status($packet_status_details, $start_date, $end_date)
    {
        $packet_detials = $packet_status_details->whereBetween('CreatedDate', [$start_date, $end_date])
            ->groupBy('StatusDetails')
            ->map(function ($row) {
                return $row->count();
            });
        $shipment_status_count = [];
        foreach ($packet_detials as $key => $value) {
            $key = trim($key);
            if (isset($shipment_status_count[$key])) {
                $shipment_status_count[$key] +=  $value;
            } else {
                $shipment_status_count[$key] = $value;
            }
        }
        return $shipment_status_count;
    }

    public function micro_status_count($micro_status, $packet_status)
    {
        $status_count = [];
        foreach ($micro_status as $micro_status_key => $micro_status_value) {
            $micro_status_key = trim($micro_status_key);
            $micro_status_value = trim($micro_status_value);
            if (isset($packet_status[$micro_status_key])) {
                if (isset($status_count[$micro_status_value])) {
                    $status_count[$micro_status_value] += $packet_status[$micro_status_key];
                } else {
                    $status_count[$micro_status_value] = $packet_status[$micro_status_key];
                }
            }
        }

        return $status_count;
    }
}
