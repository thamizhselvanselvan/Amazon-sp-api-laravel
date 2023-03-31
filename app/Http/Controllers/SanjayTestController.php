<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ShipNTrack\Courier\Courier;
use App\Models\ShipNTrack\Courier\StatusManagement;
use App\Model\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;
use App\Models\ShipNTrack\CourierTracking\SmsaTracking;
use App\Models\ShipNTrack\CourierTracking\AramexTracking;
use App\Models\ShipNTrack\CourierTracking\BombinoTracking;

class SanjayTestController extends Controller
{
    public function index()
    {
        $status_array = StatusManagement::query()
            ->select('courier_status', 'courier_id', 'stop_tracking', 'api_display')->where("stop_tracking", 1)->get()->groupBy("courier_id")->toArray();

        $tables = ["Trackingae", "Trackingin", "Trackingksa"];

        foreach ($tables as $mode) {
            if ($mode == 'Trackingae') {
                $query_model = Trackingae::query()->with('CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4');
            }
            if ($mode == 'Trackingin') {
                // $query_model = Trackingin::query()->with('CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4');
            }
            if ($mode == 'Trackingksa') {
               
                $query_model = Trackingksa::query()->with('CourierPartner1', 'CourierPartner2', 'CourierPartner3', 'CourierPartner4');
            }
        }

            $query_model->where('status', '0')
                // ->where(['forwarder_1_flag' => 0, 'forwarder_2_flag' => 0, 'forwarder_3_flag' => 0, 'forwarder_4_flag' => 0])
                ->orderBy('awb_number')
                ->chunk(100, function ($result) use ($status_array) {

                    foreach ($result as $data) {
                        //Forwarder 1 Data
                        if (isset($data->CourierPartner1->courier_id)) {
                            $details_f1['courier_id'] = $data->CourierPartner1->courier_id;
                            $details_f1['awb'] = $data->awb_number;
                            $details_f1['f1'] = $data->forwarder_1;
                            $details_f1['forwarder_1_awb'] = $data->forwarder_1_awb;
                            $details_f1['forwarder_1_flag'] = $data->forwarder_1_flag;
                        } else {
                            $this->updateFlag($data['awb_number'], 'forwarder_1_flag');
                        }
                        //Forwarder 2 Data
                        if (isset($data->CourierPartner2->courier_id)) {
                            $details_f2['courier_id'] = $data->CourierPartner2->courier_id;
                            $details_f2['awb'] = $data->awb_number;
                            $details_f2['f2'] = $data->forwarder_2;
                            $details_f2['forwarder_2_awb'] = $data->forwarder_2_awb;
                            $details_f2['forwarder_2_flag'] = $data->forwarder_2_flag;
                        } else {
                            $this->updateFlag($data['awb_number'], 'forwarder_2_flag');
                        }

                        //Forwarder 3 Data
                        if (isset($data->CourierPartner3->courier_id)) {
                            $details_f3['courier_id'] = $data->CourierPartner3->courier_id;
                            $details_f3['awb'] = $data->awb_number;
                            $details_f3['f3'] = $data->forwarder_3;
                            $details_f3['forwarder_3_awb'] = $data->forwarder_3_awb;
                            $details_f3['forwarder_3_flag'] = $data->forwarder_3_flag;
                        } else {
                            $this->updateFlag($data['awb_number'], 'forwarder_3_flag');
                        }
                        //Forwarder 4 Data
                        if (isset($data->CourierPartner4->courier_id)) {
                            $details_f4['courier_id'] = $data->CourierPartner4->courier_id;
                            $details_f4['awb'] = $data->awb_number;
                            $details_f4['f4'] = $data->forwarder_3;
                            $details_f4['forwarder_4_awb'] = $data->forwarder_4_awb;
                            $details_f4['forwarder_4_flag'] = $data->forwarder_4_flag;
                        } else {
                            $this->updateFlag($data['awb_number'], 'forwarder_4_flag');
                        }


                        //stop Tracking Check For each Forwarder
                        if (isset($details_f1['forwarder_1_flag']) && isset($status_array[$details_f1['courier_id']]) && $details_f1['forwarder_1_flag'] != 1) {
                            $this->forwardertrackingstop('forwarder_1', $details_f1, $status_array[$details_f1['courier_id']]);
                        }

                        if (isset($details_f2['forwarder_2_flag']) && isset($status_array[$details_f2['courier_id']]) && $details_f2['forwarder_2_flag'] != 1) {
                            $this->forwardertrackingstop('forwarder_2', $details_f2, $status_array[$details_f2['courier_id']]);
                        }

                        if (isset($details_f3['forwarder_3_flag']) && isset($status_array[$details_f3['courier_id']])  && $details_f3['forwarder_3_flag'] != 1) {
                            $this->forwardertrackingstop('forwarder_3', $details_f3, $status_array[$details_f3['courier_id']]);
                        }

                        if (isset($details_f4['forwarder_4_flag']) && isset($status_array[$details_f4['courier_id']]) && $details_f4['forwarder_4_flag'] != 1) {
                            $this->forwardertrackingstop('forwarder_4', $details_f4, $status_array[$details_f4['courier_id']]);
                        }


                        // if (($details_f1['forwarder_1_flag'] == 1) && ($details_f2['forwarder_2_flag'] == 1)  && ($details_f3['forwarder_3_flag'] == 1)($details_f4['forwarder_4_flag'] == 1)) {
                        //     po('all flags are 1');
                        // }
                    }
                });
        // }
    }

    public function forwardertrackingstop($forwarder, $details, $stop_status)
    {
        $stop_status = Arr::pluck($stop_status, 'courier_status');

        // Bombino Forwarder Stop 
        $c_nmae = Courier::where('id', $details['courier_id'])->select('courier_name')->get()->first();
        if (($c_nmae->courier_name) == 'Bombino') {

            $bom_data = BombinoTracking::where('awbno', $details["{$forwarder}_awb"])->select('event_detail')->latest('updated_at')->first();

            if (isset($bom_data->event_detail) && in_array($bom_data->event_detail, $stop_status)) {
                $this->updateFlag($details['awb'], "{$forwarder}_flag");
            }
        }

        // SMSA Forwarder  Stop 
        else if ($c_nmae->courier_name == 'SMSA') {
            $sms_data = SmsaTracking::where('awbno', $details["{$forwarder}_awb"])->select('activity')->latest('updated_at')->first();

            if (isset($sms_data->activity) && in_array($sms_data->activity, $stop_status)) {
                $this->updateFlag($details['awb'], "{$forwarder}_flag");
            }
        }

        // Aramex Forwarder Stop 
        else if ($c_nmae->courier_name == 'Aramex') {

            $aramax_data = AramexTracking::where('awbno', $details["{$forwarder}_awb"])->select('update_description')->latest('updated_at')->first();

            if (isset($aramax_data->update_description) && in_array($aramax_data->update_description, $stop_status)) {
                $this->updateFlag($details['awb'], "{$forwarder}_flag");
            }
        }
    }

    public function updateFlag($awb, $forwarder)
    {
        $count = 0;
        Trackingae::where(['awb_number' => $awb])->update([$forwarder => 1]);
        po($count);
        $count++;
    }
}
