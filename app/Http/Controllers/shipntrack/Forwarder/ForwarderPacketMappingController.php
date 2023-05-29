<?php

namespace App\Http\Controllers\shipntrack\Forwarder;

use League\Csv\Reader;
use League\Csv\Writer;
use AWS\CRT\HTTP\Response;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Eval_;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use App\Models\ShipNTrack\Courier\CourierPartner;
use App\Models\ShipNTrack\ForwarderMaping\IntoAE;
use App\Models\ShipNTrack\Packet\PacketForwarder;
use App\Models\ShipNTrack\ForwarderMaping\IntoKSA;
use App\Models\ShipNTrack\ForwarderMaping\USAtoAE;
use App\Models\ShipNTrack\ForwarderMaping\USAtoKSA;
use App\Models\ShipNTrack\ForwarderMaping\Trackingae;
use App\Models\ShipNTrack\ForwarderMaping\Trackingin;
use App\Models\ShipNTrack\ForwarderMaping\Trackingksa;

class ForwarderPacketMappingController extends Controller
{

    public function index(Request $request)
    {
        // $user_name = Auth::user()->name;
        // $user_email = Auth::user()->email;
        // ->where('login_user', $user_name)
        // ->where('login_email', $user_email)

        $destinations = CourierPartner::select('source', 'destination')
            ->groupBy('source', 'destination')
            ->get()
            ->toArray();

        return view('shipntrack.Forwarder.index', compact('destinations'));
    }

    public function courierget(Request $request)
    {
        // $user_name = Auth::user()->name;
        // $user_email = Auth::user()->email;
        // ->where('login_user', $user_name)
        // ->where('login_email', $user_email)

        $destination =    $request->destination;

        $partners_lists = CourierPartner::query()
            ->with(['courier_names'])
            ->where(['destination' => $destination])
            ->where('active', '1')
            ->get()
            ->toArray();

        $lists = [];
        foreach ($partners_lists as $partners_list) {
            $lists[] = [
                'id' => $partners_list['id'],
                'user_name' => $partners_list['user_name'],
                // 'courier_name' => $partners_list['courier_names']['courier_name'],
            ];
        }
        return response()->json($lists);
    }

    public function store_farwarder(Request $request)
    {

        $request->validate([
            'destination' => 'required',
            'reference' => 'required',
            'forwarder1' => 'required|not in:0',
            'forwarder_1_awb' => 'required',
            'consignor' => 'required',
            'consignee' => 'required',
        ]);

        $tracking_data = [
            'reference_id' => $request->reference,
            'consignor' => $request->consignor,
            'consignee' => $request->consignee,
            'forwarder_1' => $request->forwarder1,
            'forwarder_1_awb' => $request->forwarder_1_awb,
            'forwarder_1_flag' => 0,
            'forwarder_2' => $request->forwarder2,
            'forwarder_2_awb' => $request->forwarder_2_awb,
            'forwarder_2_flag' => 0,
            'forwarder_3' => $request->forwarder3,
            'forwarder_3_awb' => $request->forwarder_3_awb,
            'forwarder_3_flag' => 0,
            'forwarder_4' => $request->forwarder4,
            'forwarder_4_awb' => $request->forwarder_4_awb,
            'forwarder_4_flag' => 0,
            'status' => 0
        ];

        if ($request->destination == 'AE') {
            Trackingae::create(
                $tracking_data,
                // 'reference_id_unique',
                // [
                //     'forwarder_1',
                //     'forwarder_1_awb',
                //     'forwarder_2',
                //     'forwarder_2_awb',
                //     'forwarder_3',
                //     'forwarder_3_awb',
                //     'forwarder_4',
                //     'forwarder_4_awb',

                // ]
            );
        } elseif ($request->destination == 'IN') {
            Trackingin::create(
                $tracking_data,
                // 'reference_id_unique',
                // [
                //     'forwarder_1',
                //     'forwarder_1_awb',
                //     'forwarder_2',
                //     'forwarder_2_awb',
                //     'forwarder_3',
                //     'forwarder_3_awb',
                //     'forwarder_4',
                //     'forwarder_4_awb',

                // ]
            );
        } elseif ($request->destination == 'KSA') {
            Trackingksa::create(
                $tracking_data,
                // 'reference_id_unique',
                // [
                //     'forwarder_1',
                //     'forwarder_1_awb',
                //     'forwarder_2',
                //     'forwarder_2_awb',
                //     'forwarder_3',
                //     'forwarder_3_awb',
                //     'forwarder_4',
                //     'forwarder_4_awb',

                // ]
            );
        }

        return redirect()->intended('/shipntrack/forwarder/')->with("success", "Tracking Details Uploaded Successfully");
    }

    public function Upload()
    {
        return view('shipntrack.Forwarder.upload');
    }

    public function templateDownload()
    {
        $file_path = public_path('template/Forwarder-Tracking-Template.csv');
        return response()->download($file_path);
    }

    public function save(Request $request)
    {
        $request->validate([
            'forwarder_awb' => 'required|mimes:csv,txt,xls,xlsx'
        ]);

        $path = 'ShipnTrack/Forwarder/Tracking_No.csv';

        $source = file_get_contents($request->forwarder_awb);

        Storage::put($path, $source);

        $csv = Reader::createFromPath(Storage::path($path), 'r');
        $csv->setDelimiter(",");
        $csv->setHeaderOffset(0);

        $forwarder_details = [];

        $awb = [
            'Smsa' => '',
            'Bombino' => '',
        ];

        foreach ($csv as $key => $value) {

            foreach ($value as $key => $courier) {

                if (strtoupper($courier) == 'SMSA') {

                    $smas_key = $key . '_awb';
                    $awb['Smsa'] = $value[$smas_key];
                } elseif (strtoupper($courier) == 'BOMBINO') {

                    $bombino_key = $key . '_awb';
                    $awb['Bombino'] = $value[$bombino_key];
                }
            }

            $forwarder_details[] = $awb;
            $tracking[] =  $value;
        }
        PacketForwarder::upsert(
            $tracking,
            'order_id_awb_no_unique',
            [
                'order_id',
                'awb_no',
                'forwarder_1',
                'forwarder_1_awb',
                'forwarder_2',
                'forwarder_2_awb'
            ]
        );

        foreach ($forwarder_details as $value) {
            foreach ($value as $key => $awb_no) {

                if ($key == 'Smsa') {

                    $class = 'ShipNTrack\\SMSA\\SmsaGetTracking';
                    $queue_type = 'tracking';
                    $awbNo_array = [$awb_no];
                    jobDispatchFunc(class: $class, parameters: $awbNo_array, queue_type: $queue_type);
                } elseif ($key == 'Bombino') {

                    $class = "ShipNTrack\\Bombino\\BombinoGetTracking";
                    $parameters['awb_no'] = $awb_no;
                    $queue_type = 'tracking';
                    jobDispatchFunc(class: $class, parameters: $parameters, queue_type: $queue_type);
                }
            }
        }
        return redirect()->intended('/shipntrack/forwarder/upload')->with("success", "Tracking Details Uploaded");
    }

    public function missingexpview()
    {
        return view('shipntrack.Forwarder.export');
    }

    public function missexport(Request $request)
    {
        if ($request->ajax()) {
            $records = [];

            $filter  = explode('!=', $request->selected);

            $date = $filter[0];
            $first_forwarder = $filter[1];
            $second_forwarder = $filter[2];

            $first_forwarder  = $first_forwarder == 'false' ? NULL : $first_forwarder;
            $second_forwarder  = $second_forwarder == 'false' ? NULL : $second_forwarder;

            $dbheaders = ['order_id', 'awb_no', 'forwarder_1', 'forwarder_1_awb', 'forwarder_2', 'forwarder_2_awb'];

            $records = PacketForwarder::select($dbheaders)
                ->when(!empty(trim($date)), function ($query) use ($date) {
                    $date = $this->split_date($date);
                    $query->whereBetween('created_at', [$date[0], $date[1]]);
                })
                ->when(!is_null($first_forwarder), function ($query) {

                    $query->where('forwarder_1', '');
                })
                ->when(!is_null($second_forwarder), function ($query) {

                    $query->where('forwarder_2', '');
                })
                ->get();

            $headers = [

                'order ID',
                'AWB no',
                'forwarder 1',
                'forwarder_1_awb',
                'forwarder_2',
                'forwarder_2_awb'

            ];
            $exportFilePath = 'farwarder\missing.csv';
            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $writer->insertOne($headers);

            $writer->insertAll($records->toArray());
        }
    }
    public function downexp()
    {
        return Storage::download('farwarder\missing.csv');
    }

    public function split_date($date)
    {
        $date = explode(' - ', $date);
        return [trim($date[0]), trim($date[1])];
    }
    public function singlesearch(Request $request)
    {
        $partners_lists = CourierPartner::get();
        $order_id = $request->orderid;
        // $name = PacketForwarder::where('order_id', $order_id)->get();
        // dd($name[0]->id);

        $order = config('database.connections.order.database');
        $order_item = $order . '.orderitemdetails';

        $data = DB::connection('shipntracking')
            ->select("SELECT * from packet_forwarders as pf 
                        join $order_item as oid on pf.order_id = oid.amazon_order_identifier
                        where pf.order_id='$order_id'  ");

        if ($data == []) {
            // return back()->with('error', "No Data Found OR Invalid OrderID");
            return redirect()->intended('/shipntrack/forwarder')->with('error', 'No Data Found OR Invalid OrderID');
        }

        $name = PacketForwarder::where('order_id', $order_id)->get();
        $selected_forwarder_1 = $name[0]->forwarder_1;
        $selected_forwarder_2 = $name[0]->forwarder_2;

        return view('shipntrack.Forwarder.index', compact('partners_lists', 'data', 'selected_forwarder_1', 'selected_forwarder_2'));
    }

    public function forwarderupdate(Request $request)
    {
        if ($request->forwarder1 == 0 || $request->forwarder2 == 0) {
            return redirect()->route('shipntrack.forwarder')->with('success', 'forwarder not selected properly');
        }
        $courire_code1 = CourierPartner::query()
            ->select('courier_code')
            ->where('courier_code', $request->forwarder1)
            ->get();

        $courire_code2 = CourierPartner::query()
            ->select('courier_code')
            ->where('courier_code', $request->forwarder2)
            ->get();
        // dd($request->forwarder2);
        $order_id = $request->order_id;
        $validated = ([
            'forwarder_1' => $courire_code1[0]->courier_code,
            'forwarder_1_awb' => $request->forwarder_1_awb,
            'forwarder_2' =>  $courire_code2[0]->courier_code,
            'forwarder_2_awb' => $request->forwarder_2_awb
        ]);
        PacketForwarder::where('order_id', $order_id)->update($validated);
        return redirect()->route('shipntrack.forwarder')->with('success', 'packet forwarders has updated successfully');
    }

    public function listing(Request $request)
    {

        // if ($request->ajax()) {
        //     $mode = $request->mode;

        //     $data = IntoAE::query()->get();
        //     if ($mode == 'IN_KSA') {
        //         $data = IntoKSA::query()->get();
        //     } else  if ($mode == 'USA_AE') {
        //         $data = USAtoAE::query()->get();
        //     } else  if ($mode == 'USA_KSA') {
        //         $data = USAtoKSA::query()->get();
        //     }

        //     return DataTables::of($data)
        //         ->make(true);
        // }
        return view('shipntrack.Forwarder.listing');
    }

    public function editshipment(Request $request)
    {
        $source_destination = $request->destination;
        return view('shipntrack.Forwarder.edit', compact('source_destination'));
    }

    public function editdata(Request $request)
    {
        $id = $request->id;
        if ($request->destination == 'AE') {
            $data =   Trackingae::where('reference_id', $id)->get();
        } elseif ($request->destination == 'IN') {
            $data =  Trackingin::where('reference_id', $id)->get();
        } elseif ($request->destination == 'KSA') {
            $data =    Trackingksa::where('reference_id', $id)->get();
        }
        if (count($data) == 0) {
            return response()->json(['eror_data' => 'Invalid Refrence ID Please check']);
        }

        $user_name = Auth::user()->name;
        $user_email = Auth::user()->email;

        $destination =    $request->destination;

        $partners_lists = CourierPartner::query()
            ->with(['courier_names'])
            ->where('login_user', $user_name)
            ->where('login_email', $user_email)
            ->where(['destination' => $destination])
            ->get()
            ->toArray();

        $lists = [];
        foreach ($partners_lists as $partners_list) {
            $lists[] = [
                'id' => $partners_list['id'],
                'user_name' => $partners_list['user_name'],
            ];
        }

        $responce = [
            'ref_data' => $data,
            'forwarder_data' => $lists,
        ];

        return response()->json($responce);
    }
    public function edit_store(Request $request)
    {

        $request->validate([
            'destination' => 'required',
            'reference' => 'required',
            'forwarder1' => 'required|not in:0',
            'forwarder_1_awb' => 'required',
            'consignor' => 'required',
            'consignee' => 'required',
        ]);

        $tracking_data = [
            'reference_id' => $request->reference,
            'consignor' => $request->consignor,
            'consignee' => $request->consignee,
            'forwarder_1' => $request->forwarder1,
            'forwarder_1_awb' => $request->forwarder_1_awb,
            // 'forwarder_1_flag' => 0,
            'forwarder_2' => $request->forwarder2,
            'forwarder_2_awb' => $request->forwarder_2_awb,
            // 'forwarder_2_flag' => 0,
            'forwarder_3' => $request->forwarder3,
            'forwarder_3_awb' => $request->forwarder_3_awb,
            // 'forwarder_3_flag' => 0,
            'forwarder_4' => $request->forwarder4,
            'forwarder_4_awb' => $request->forwarder_4_awb,
            // 'forwarder_4_flag' => 0,
            // 'status' => 0
        ];

        if ($request->destination == 'AE') {
            Trackingae::where('reference_id', $request->reference)
                ->update($tracking_data);
        } elseif ($request->destination == 'IN') {
            Trackingin::where('reference_id', $request->reference)
                ->update($tracking_data);
        } elseif ($request->destination == 'KSA') {
            Trackingksa::where('reference_id', $request->reference)
                ->update($tracking_data);
        }

        return redirect()->intended('shipntrack/shipment/edit/' . $request->destination)->with("success", "Tracking Details Updated Successfully");
    }
}
