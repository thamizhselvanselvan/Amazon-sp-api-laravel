<?php

namespace App\Console\Commands\Shipntrack\Tracking;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use App\Models\ShipNTrack\Courier\Courier;
use App\Models\ShipNTrack\Courier\StatusManagement;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;
use App\Models\ShipNTrack\CourierTracking\SmsaTracking;
use App\Models\ShipNTrack\CourierTracking\AramexTracking;
use App\Models\ShipNTrack\CourierTracking\BombinoTracking;

class StopTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:shipntrack_stop_tracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cheack The Status For Each Forwarder And Stops Tracking If courier Status Is matched With Stop status';

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
        $status_array = StatusManagement::query()
            ->select('courier_status', 'courier_id', 'stop_tracking', 'api_display')
            ->where("stop_tracking", 1)
            ->get()
            ->groupBy("courier_id")
            ->toArray();
        $tables = ["Trackingae", "Trackingin", "Trackingksa"];
        foreach ($tables as $mode) {

            if ($mode == 'Trackingae') {

                $query_model = Trackingae::query();
                $model_table = 'Trackingae';
            }

            if ($mode == 'Trackingin') {
                $query_model = Trackingin::query();
                $model_table = 'Trackingin';
            }

            if ($mode == 'Trackingksa') {

                $query_model = Trackingksa::query();
                $model_table = 'Trackingksa';
            }
            $this->query($model_table, $query_model, $status_array);
        }
    }
    
    // Select each Forwarder Data And Check For Stop status
    public function query($table, $model, $status_array)
    {

        return $model->with('CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4')
            ->where('status', '0')
            ->orderBy('awb_number')
            ->chunk(100, function ($result) use ($status_array, $table) {

                foreach ($result as $data) {
                    //Forwarder 1 Data
                    if (isset($data->CourierPartner1->courier_id)) {
                        $details_f1['courier_id'] = $data->CourierPartner1->courier_id;
                        $details_f1['awb'] = $data->awb_number;
                        $details_f1['f1'] = $data->forwarder_1;
                        $details_f1['forwarder_1_awb'] = $data->forwarder_1_awb;
                        $details_f1['forwarder_1_flag'] = $data->forwarder_1_flag;
                    } else {
                        $this->updateFlag($table, $data['awb_number'], 'forwarder_1_flag');
                    }

                    //Forwarder 2 Data
                    if (isset($data->CourierPartner2->courier_id)) {
                        $details_f2['courier_id'] = $data->CourierPartner2->courier_id;
                        $details_f2['awb'] = $data->awb_number;
                        $details_f2['f2'] = $data->forwarder_2;
                        $details_f2['forwarder_2_awb'] = $data->forwarder_2_awb;
                        $details_f2['forwarder_2_flag'] = $data->forwarder_2_flag;
                    } else {
                        $this->updateFlag($table, $data['awb_number'], 'forwarder_2_flag');
                    }

                    //Forwarder 3 Data
                    if (isset($data->CourierPartner3->courier_id)) {
                        $details_f3['courier_id'] = $data->CourierPartner3->courier_id;
                        $details_f3['awb'] = $data->awb_number;
                        $details_f3['f3'] = $data->forwarder_3;
                        $details_f3['forwarder_3_awb'] = $data->forwarder_3_awb;
                        $details_f3['forwarder_3_flag'] = $data->forwarder_3_flag;
                    } else {
                        $this->updateFlag($table, $data['awb_number'], 'forwarder_3_flag');
                    }
                    //Forwarder 4 Data
                    if (isset($data->CourierPartner4->courier_id)) {
                        $details_f4['courier_id'] = $data->CourierPartner4->courier_id;
                        $details_f4['awb'] = $data->awb_number;
                        $details_f4['f4'] = $data->forwarder_3;
                        $details_f4['forwarder_4_awb'] = $data->forwarder_4_awb;
                        $details_f4['forwarder_4_flag'] = $data->forwarder_4_flag;
                    } else {
                        $this->updateFlag($table, $data['awb_number'], 'forwarder_4_flag');
                    }

                    //stop Tracking Check For each Forwarder
                    if (isset($details_f1['forwarder_1_flag']) && isset($status_array[$details_f1['courier_id']]) && $details_f1['forwarder_1_flag'] != 1) {

                        $this->forwardertrackingstop($table, 'forwarder_1', $details_f1, $status_array[$details_f1['courier_id']]);
                    }

                    if (isset($details_f2['forwarder_2_flag']) && isset($status_array[$details_f2['courier_id']]) && $details_f2['forwarder_2_flag'] != 1) {
                        $this->forwardertrackingstop($table, 'forwarder_2', $details_f2, $status_array[$details_f2['courier_id']]);
                    }

                    if (isset($details_f3['forwarder_3_flag']) && isset($status_array[$details_f3['courier_id']])  && $details_f3['forwarder_3_flag'] != 1) {
                        $this->forwardertrackingstop($table, 'forwarder_3', $details_f3, $status_array[$details_f3['courier_id']]);
                    }

                    if (isset($details_f4['forwarder_4_flag']) && isset($status_array[$details_f4['courier_id']]) && $details_f4['forwarder_4_flag'] != 1) {
                        $this->forwardertrackingstop($table, 'forwarder_4', $details_f4, $status_array[$details_f4['courier_id']]);
                    }
                }
            });
    }

    public function forwardertrackingstop($table, $forwarder, $details, $stop_status)
    {

        $stop_status = Arr::pluck($stop_status, 'courier_status');

        // Bombino Forwarder Stop 
        $c_nmae = Courier::where('id', $details['courier_id'])->select('courier_name')->get()->first();
        if (($c_nmae->courier_name) == 'Bombino') {
            $bom_data = BombinoTracking::where('awbno', $details["{$forwarder}_awb"])->select('event_code')->orderBy('updated_at', 'desc')->first();

            if (isset($bom_data->event_code) && in_array($bom_data->event_code, $stop_status)) {
                $this->updateFlag($table, $details['awb'], "{$forwarder}_flag");
            }
        }

        // SMSA Forwarder  Stop 
        else if ($c_nmae->courier_name == 'SMSA') {
            $sms_data = SmsaTracking::where('awbno', $details["{$forwarder}_awb"])->select('activity')->orderBy('updated_at', 'desc')->first();

            if (isset($sms_data->activity) && in_array($sms_data->activity, $stop_status)) {
                $this->updateFlag($table, $details['awb'], "{$forwarder}_flag");
            }
        }

        // Aramex Forwarder Stop 
        else if ($c_nmae->courier_name == 'Aramex') {

            $aramax_data = AramexTracking::where('awbno', $details["{$forwarder}_awb"])->select('update_description')->orderBy('updated_at', 'desc')->first();
            if (isset($aramax_data->update_description) && in_array($aramax_data->update_description, $stop_status)) {
                $this->updateFlag($table, $details['awb'], "{$forwarder}_flag");
            }
        }
    }

    //update Forwarder Flag to 1 from 0 if status(Stop) Found
    public function updateFlag($table, $awb, $forwarder)
    {

        if ($table == 'Trackingae') {
            Trackingae::where(['awb_number' => $awb])->update([$forwarder => 1]);
            $this->update_status($table, $awb);
        } else if ($table == 'Trackingin') {
            Trackingin::where(['awb_number' => $awb])->update([$forwarder => 1]);
            $this->update_status($table, $awb);
        } else if ($table == 'Trackingksa') {
            Trackingksa::where(['awb_number' => $awb])->update([$forwarder => 1]);
            $this->update_status($table, $awb);
        }
    }

    //updated Status to 1 if all Forwarders Tracking Is Done
    public function update_status($table, $awb)
    {
        if ($table == 'Trackingae') {
            $ae_data =  Trackingae::where(['awb_number' => $awb])->select('forwarder_1_flag', 'forwarder_2_flag', 'forwarder_3_flag', 'forwarder_4_flag')->get();
            $value = ($ae_data[0]);
            if ($value->forwarder_1_flag == 1 && $value->forwarder_2_flag == 1 && $value->forwarder_3_flag == 1 && $value->forwarder_4_flag == 1) {
                Trackingae::where(['awb_number' => $awb])->update(['status' => 1]);
            }
        } else if ($table == 'Trackingin') {
            $in_data =   Trackingin::where(['awb_number' => $awb])->select('forwarder_1_flag', 'forwarder_2_flag', 'forwarder_3_flag', 'forwarder_4_flag')->get();
            $data_in = ($in_data[0]);

            if ($data_in->forwarder_1_flag == 1 && $data_in->forwarder_2_flag == 1 && $data_in->forwarder_3_flag == 1 && $data_in->forwarder_4_flag == 1) {
                Trackingin::where(['awb_number' => $awb])->update(['status' => 1]);
            }
        } else if ($table == 'Trackingksa') {

            $ksa_data = Trackingksa::where(['awb_number' => $awb])->select('forwarder_1_flag', 'forwarder_2_flag', 'forwarder_3_flag', 'forwarder_4_flag')->get();
            $data_ksa = ($ksa_data[0]);
            if ($data_ksa->forwarder_1_flag == 1 && $data_ksa->forwarder_2_flag == 1 && $data_ksa->forwarder_3_flag == 1 && $data_ksa->forwarder_4_flag == 1) {
                Trackingksa::where(['awb_number' => $awb])->update(['status' => 1]);
            }
        }
    }
}
