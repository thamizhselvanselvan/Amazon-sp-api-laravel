<?php

namespace App\Http\Controllers\shipntrack\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\Courier\StatusManagement;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

class B2CShipTrackingAPIController extends Controller
{
    public function B2CShipTrackingAPIResponse(Request $request)
    {
        $OrderByColunm1 = [
            'SMSA' => 'date',
            'Aramex' => 'update_date_time',
            'Bombino' => 'action_date'

        ];

        $OrderByColunm2 = [
            'SMSA' => 'date',
            'Aramex' => 'update_date_time',
            'Bombino' => 'action_time'

        ];
        $selectColumns = [
            'SMSA' => [
                'date',
                'activity',
                'location',
            ],
            'Aramex' => [
                'update_date_time',
                'update_description',
                'update_location',
            ],
            'Bombino' => [
                'destination',
                'origin',
                'event_detail',
                'action_date',
                'action_time',
                'event_code',
                'location',
            ],

        ];

        $destination = substr($request->awbNo, 0, 2);
        $awbNo = substr($request->awbNo, 2);

        $data = [];
        if ($destination == 'AE') {

            $data = Trackingae::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('id', $awbNo)
                ->get()
                ->toArray();
        } elseif ($destination == 'IN') {

            $data = Trackingin::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('id', $awbNo)
                ->get()
                ->toArray();
        } elseif ($destination == 'SA') {

            $data = Trackingksa::with([
                'CourierPartner1.courier_names',
                'CourierPartner2.courier_names',
                'CourierPartner3.courier_names',
                'CourierPartner4.courier_names'
            ])
                ->where('awb_number', $awbNo)
                ->get()
                ->toArray();
        }

        $packet_details = [];
        $forwarder1_result = [];
        $forwarder2_result = [];

        foreach ($data as $key => $records) {

            $packet_details[] = [
                'consignor' => json_decode($records['consignor_details'])->consignor ?? 'test',
                'consignee' => json_decode($records['consignee_details'])->consignee ?? 'test',
                'destination' => $records['courier_partner1']['destination'],
                'origin' => $records['courier_partner1']['source'],
            ];

            if (($records['forwarder_1_awb'] != '')) {

                $awb_no = $records['forwarder_1_awb'];
                $courier_name = $records['courier_partner1']['courier_names']['courier_name'];

                $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

                $forwarder1_data = $table->select($selectColumns[$courier_name])
                    ->where('awbno', $awb_no)
                    ->orderBy($OrderByColunm1[$courier_name], 'DESC')
                    ->orderBy($OrderByColunm2[$courier_name], 'DESC')
                    ->get()
                    ->toArray();

                $columnName = $records['forwarder_2_awb'] == '' ? 'last_mile_status' : 'first_mile_status';
                $courierActivities = StatusManagement::select('courier_status')
                    ->join('courier', 'courier.id', '=', 'status_master.courier_id')
                    ->where($columnName, 1)
                    ->where('courier_name', $courier_name)
                    ->get()
                    ->toArray();

                $courierStatus = [];
                foreach ($courierActivities as $courierActivity) {
                    $courierStatus[] = $courierActivity['courier_status'];
                }

                foreach ($forwarder1_data as $key => $data) {
                    if ($courier_name == 'Bombino') {

                        if (in_array(strtoupper($data['event_detail']), $courierStatus)) {

                            $forwarder1_result[$key] = $data;
                        }
                    } else if ($courier_name == 'SMSA') {

                        if (in_array(strtoupper($data['activity']), $courierStatus)) {
                            $forwarder1_result[$key] = [
                                'event_detail' => $data['activity'],
                                'event_code' => $data['activity'],
                                'action_date' => date('Y-m-d', strtotime($data['date'])),
                                'action_time' => date('H:i:s', strtotime($data['date'])),
                                'location' => $data['location']
                            ];
                        }
                    } else if ($courier_name == 'Aramex') {

                        if (in_array(strtoupper($data['update_description']), $courierStatus)) {
                            $forwarder1_result[$key] = [
                                'event_detail' => $data['update_description'],
                                'event_code' => $data['update_description'],
                                'action_date' => date('Y-m-d', strtotime($data['update_date_time'])),
                                'action_time' => date('H:i:s', strtotime($data['update_date_time'])),
                                'location' => $data['update_location']
                            ];
                        }
                    };
                }
            }

            if (($records['forwarder_2_awb'] != '')) {

                $awb_no = $records['forwarder_2_awb'];
                $courier_name = $records['courier_partner2']['courier_names']['courier_name'];
                $table = table_model_change(model_path: 'CourierTracking', model_name: ucwords(strtolower($courier_name)) . 'Tracking', table_name: strtolower($courier_name) . '_trackings');

                $forwarder2_data = $table->select($selectColumns[$courier_name])
                    ->where('awbno', $awb_no)
                    ->orderBy($OrderByColunm1[$courier_name], 'DESC')
                    ->orderBy($OrderByColunm2[$courier_name], 'DESC')
                    ->get()
                    ->toArray();

                $columnName = $records['forwarder_3_awb'] == '' ? 'last_mile_status' : 'first_mile_status';
                $courierActivities = StatusManagement::select('courier_status')
                    ->join('courier', 'courier.id', '=', 'status_master.courier_id')
                    ->where($columnName, 1)
                    ->where('courier_name', $courier_name)
                    ->get()
                    ->toArray();

                $courierStatus = [];
                foreach ($courierActivities as $courierActivity) {
                    $courierStatus[] = $courierActivity['courier_status'];
                }

                foreach ($forwarder2_data as $key => $data) {
                    if ($courier_name == 'Bombino') {
                        if (in_array(strtoupper($data['event_detail']), $courierStatus)) {
                            $forwarder2_result[$key] = $data;
                        }
                    } elseif ($courier_name == 'SMSA') {
                        if (in_array(strtoupper($data['activity']), $courierStatus)) {

                            $forwarder2_result[$key] = [
                                'event_detail' => $data['activity'],
                                'event_code' => $data['activity'],
                                'action_date' => date('Y-m-d', strtotime($data['date'])),
                                'action_time' => date('H:i:s', strtotime($data['date'])),
                                'location' => $data['location']
                            ];
                        }
                    } elseif ($courier_name == 'Aramex') {

                        if (in_array(strtoupper($data['update_description']), $courierStatus)) {

                            $forwarder2_result[$key] = [
                                'event_detail' => $data['update_description'],
                                'event_code' => $data['update_description'],
                                'action_date' => date('Y-m-d', strtotime($data['update_date_time'])),
                                'action_time' => date('H:i:s', strtotime($data['update_date_time'])),
                                'location' => $data['update_location']
                            ];
                        }
                    }
                }
            }
        }
        $result = [...$packet_details, ...$forwarder2_result, ...$forwarder1_result];

        return $result;
    }
}
