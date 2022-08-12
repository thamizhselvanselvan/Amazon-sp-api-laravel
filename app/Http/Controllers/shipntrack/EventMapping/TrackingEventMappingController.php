<?php

namespace App\Http\Controllers\shipntrack\EventMapping;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Models\ShipNTrack\SMSA\SmsaTrackings;
use App\Models\ShipNTrack\Bombino\BombinoTrackingDetails;
use App\Models\ShipNTrack\EventMaster\TrackingEventMaster;
use App\Models\ShipNTrack\EventMapping\TrackingEventMapping;


class TrackingEventMappingController extends Controller
{
    public function array()
    {
        return $array = ['0'=>'Bombino', '1'=> 'SAMSA', '2'=> 'Emirate'];
    }

    public function index(Request $request)
    {   
        $arrays = $this->array();
        $master_record = TrackingEventMaster::select('event_code', 'description')->get();
        if($request->ajax())
        {
            
            $records = TrackingEventMapping::with(['TrackingEventMaster'])->orderBy('our_event_code', 'ASC')->get();
            foreach($records as $record)
            {
                return DataTables::of($records)
                ->addColumn('action', function ($result) {
                      $action = '<div class="d-flex pl-2 edit event-master-btn "><a href="/shipntrack/event-mapping/edit/' . $result->id . ' " class=" btn btn-sm text-success" ><i class="fas fa-edit"></i></a>';
                      $action .= '<div class="d-flex pl-2 delete event-master-btn "><a href="/shipntrack/event-mapping/delete/' . $result->id . ' " class=" btn btn-sm text-danger"><i class="fa fa-trash"></i></a>';
                      return $action;
                  })
                  ->addColumn('status', function($result){
                    return $result->active == 1 ? 'Yes': 'No';
                  })
                  ->addColumn('master_description', function($result){
                    return $result->TrackingEventMaster->description;
                  })
                  ->rawColumns(['action','status', 'master_description'])
                  ->make(true);
            }
        }
        return view('shipntrack.EventMapping.index', compact('master_record', 'arrays'));
    }
    public function MappingSource(Request $request)
    {
        
        if($request->ajax())
        {
            $key = $request->source;
            $records = [] ;
            $array_tables = [
                [
                    'Table_name' => 'BombinoTrackingDetails',
                    'Table_column' => 'exception',
                    'Model_path'=> 'Bombino\\'
                ],
                [
                    'Table_name' => 'SmsaTrackings',
                    'Table_column' => 'activity',
                    'Model_path'=> 'SMSA\\'
                ],
                ];
            
            $table_name = $array_tables[$key]['Table_name'];
            $table_column = $array_tables[$key]['Table_column'];
            $model_path = $array_tables[$key]['Model_path'];
            
            $table_model = table_model_change(model_path:$model_path, table_name:$table_name);
            
            $data = $table_model::get()->unique($table_column);
            foreach($data as  $value)
            {
    
                $records []= $value->$table_column;
            }
            return response()->json($records);
        }
        return view('shipntrack.EventMapping.index');
    }

    public function EventMappingRecordInsert(Request $request)
    {
        $validate = $request->validate([
            'source' => 'required',
            'our_event_code' => 'required',
            'event_source' => 'required',
            'event_description' => 'required',

        ]);
        $key = $request->source;
        $array = ['0' => 'Bombino', '1' => 'SAMSA', '2' => 'Emirate Post'];
        $source_name = $array[$key];
        
        if($request->event_check != 'on')
        {
         $event_check = 0;
        }else{
            $event_check = 1;
        }
        
        TrackingEventMapping::insert([
            'master_event_code' => $request->event_source,
            'source' => $source_name,
            'our_event_code' => $request->our_event_code,
            'our_event_description' => $request->event_description,
            'active' => $event_check,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->intended('/shipntrack/event-mapping')->with('success', 'Record insert successfully!');
    }

    public function EventMappingRecordEdit($id)
    {
        $arrays = $this->array();
        $master_record = TrackingEventMaster::select('event_code', 'description')->get();
        $records = TrackingEventMapping::find($id);
        $selected_source = $records->source;
        $selected_description = $records->our_event_description;
        $selected_event = $records->master_event_code;
        // po($selected_description);
        return view('shipntrack.EventMapping.index', compact('records', 'arrays', 'master_record', 'selected_event', 'selected_source', 'selected_description'));
    }

    public function EventMappingRecordUpdate(Request $request, $id)
    {
        $validated = $request->validate([
            'source' => 'required',
            'event_description' => 'required',
            'event_source' => 'required',
            'our_event_code' => 'required',
        ]);

        $key = $request->source;
        $array = ['0' => 'Bombino', '1' => 'SAMSA', '2' => 'Emirate Post'];
        $source_name = $array[$key];

        $update = TrackingEventMapping::find($id);
        $update->source = $source_name;
        $update->master_event_code = $request->event_source;
        $update->our_event_code = $request->our_event_code;
        $update->our_event_description = $request->event_description;
        $update->active = $request->event_check == 'on' ? 1 : 0 ;
        $update->update();

        return redirect()->intended('/shipntrack/event-mapping')->with('success', 'Record has been updated successfully!');
    }

    public function EventMappingRecordDelete($id)
    {
        TrackingEventMapping::where('id', $id)->delete();
        return redirect()->intended('/shipntrack/event-mapping')->with('success', 'Record has been deleted successfully!');
    }
}
