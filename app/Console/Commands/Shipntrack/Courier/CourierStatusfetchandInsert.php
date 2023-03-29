<?php

namespace App\Console\Commands\Shipntrack\Courier;

use Illuminate\Console\Command;
use App\Models\ShipNTrack\Courier\Courier;
use App\Models\ShipNTrack\Courier\StatusManagement;
use App\Models\ShipNTrack\CourierTracking\SmsaTracking;
use App\Models\ShipNTrack\CourierTracking\AramexTracking;
use App\Models\ShipNTrack\CourierTracking\BombinoTracking;

class CourierStatusfetchandInsert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:shipntrack_status_fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Will Check Unique Status From All Courier Partners and Insert Into Courier Status Master';

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
        // SMSA Status Fetch and Insert Into Status Master
        $smsa_data =  SmsaTracking::query()
            ->select('activity')
            ->distinct()
            ->get();

        $courier_code_sm =   Courier::query()->where('courier_name', 'SMSA')->select('id')->get();
        $code_sm = '';
        if (isset($courier_code_sm[0]->id) && (count($smsa_data) > 0)) {
            $code_sm = $courier_code_sm[0]->id;

            foreach ($smsa_data as $datas) {
                $data = [
                    'courier_id' => $code_sm,
                    'courier_status' => $datas->activity
                ];
                StatusManagement::upsert($data, ['cp_status_cp_id_unique'], ['courier_id', 'courier_status']);
            }
        }


        // Bombino Status Fetch and Insert Into Status Master
        $bombono_data = BombinoTracking::query()
            ->select('event_code')
            ->distinct()
            ->get();

        $courier_code_bom =   Courier::query()->where('courier_name', 'Bombino')->select('id')->get();
        $code_bom = '';
        if (isset($courier_code_bom[0]->id) && (count($bombono_data) > 0)) {

            $code_bom = $courier_code_bom[0]->id;

            foreach ($bombono_data as $data_bom) {
                $data = [
                    'courier_id' => $code_bom,
                    'courier_status' => $data_bom->event_code
                ];
                StatusManagement::upsert($data, ['cp_status_cp_id_unique'], ['courier_id', 'courier_status']);
            }
        }

        // Aramax Status Fetch and Insert Into Status Master
        $aramax_data = AramexTracking::query()
            ->select('update_description')
            ->distinct()
            ->get();

        $courier_code_aramax =   Courier::query()->where('courier_name', 'Aramex')->select('id')->get();
        $code_aramax = '';
        if (isset($courier_code_aramax[0]->id) && count($aramax_data) > 0) {
            $code_aramax = $courier_code_aramax[0]->id;

            foreach ($aramax_data as $data_bom) {
                $data = [
                    'courier_id' => $code_aramax,
                    'courier_status' => $data_bom->update_description
                ];
                StatusManagement::upsert($data, ['cp_status_cp_id_unique'], ['courier_id', 'courier_status']);
            }
        }
    }
}
