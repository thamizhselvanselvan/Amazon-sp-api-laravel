<?php

namespace App\Http\Controllers\shipntrack\EventMaster;

use file;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\EventMaster\TrackingEvent;
use App\Models\ShipNTrack\EventMaster\TrackingEventMaster;

class TrackingEventMasterController extends Controller
{
  public function TrackingEventRecordInsert(Request $request)
  {
      
      $validated = $request->validate([
        "event_code" => "required",
        "event_desc" => "required",
      ]);
      
      if($request->event_check != 'on')
      {
        $event_check = 0;
      }else{
        $event_check = 1;
      }
      
      TrackingEventMaster::insert([
        'event_code' => $request->event_code,
        'description' => $request->event_desc,
        'active'  => $event_check
      ]);
      
      return redirect()->intended('/shipntrack/event-master')->with('success', 'Record create successfully!');
  }
  
   public function index(Request $request)
   {  
     if ($request->ajax()) {
      
      $results = TrackingEventMaster::orderBy('id', 'DESC')->get();
        foreach($results as $result){
          return DataTables::of($results)
              ->addColumn('action', function ($result) {
                  $action = '<div class="d-flex pl-2 event-master-btn "><a href="/shipntrack/event-master/' . $result->id . ' " class=" btn btn-sm text-success" ><i class="fas fa-edit"></i></a>';
                  $action .= '<div class="d-flex pl-2 delete event-master-btn "><a href="/shipntrack/event-master/delete/' . $result->id . ' " class=" btn btn-sm text-danger"><i class="fa fa-trash"></i></a>';
                  return $action;
              })
              ->addColumn('status', function($result){
                if($result->active == 1)
                {
                  return 'YES';
                }else{
                  
                  return "NO";
                }
              })
              ->rawColumns(['action','status'])
              ->make(true);
          }
        
      }
        return view('shipntrack.EventMaster.index');
   }

   public function upload()
   {
     return view('shipntrack.EventMaster.upload');
   }

   public function TrackingEventFileSave(Request $request)
   {    
      $validated = $request->validate([
          'courier_partner' => 'required',
          'tracking_event_csv' => ['required', 'mimes:csv', 'max:2048'],
      ]);

      $courier_partner = $request->courier_partner;
      $file = $request->tracking_event_csv;
      $file = $request->file('tracking_event_csv')->storeAs('CSV/import/','trackingEventMaster.csv');

      $file_path = "CSV/import/trackingEventMaster.csv";
      if(!Storage::exists($file_path))
      {
        Storage::put($file_path, '');
      }
      
      if(App::environment(['Production', 'Staging', 'production', 'staging']))
      {
          $base_path = base_path();
          $command = "cd $base_path && php artisan mosh:tracking-event-master $courier_partner > /dev/null &";
          exec($command);
      }else{
          Artisan::call('mosh:tracking-event-master'.' '. $courier_partner);
      }
      
      return redirect()->intended('/shipntrack/event-master')->with('success', 'File upload successfully!');
   }

   public function EventMasterEdit($id)
   {
     
      $records = TrackingEventMaster::find($id);
      return view('shipntrack.EventMaster.index', compact('records'));
      
   }

   public function EventMasterUpdate(Request $request, $id)
   {
      $validated = $request->validate([
        'event_code' => 'required',
        'event_desc' => 'required',
      ]);
      
      $record = TrackingEventMaster::find($id);
      if($request->event_check != 'on')
      {
        $event_check = 0;
      }else{
        $event_check = 1;
      }
      $record->event_code = $request->event_code;
      $record->description = $request->event_desc;
      $record->active = $event_check;
      $record->update();
      
      return redirect()->intended('/shipntrack/event-master')->with('success', 'Event update successfully!');
   }

   public function EventMasterDelete($id)
   {
    $trash = TrackingEventMaster::where('id', $id)->delete();
    return redirect()->intended('/shipntrack/event-master')->with('success', 'Event delete successfully!');
    
   }
}
