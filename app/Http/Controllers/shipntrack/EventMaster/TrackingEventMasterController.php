<?php

namespace App\Http\Controllers\shipntrack\EventMaster;

use file;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\EventMaster\TrackingEventMaster;

class TrackingEventMasterController extends Controller
{
   public function index(Request $request)
   {
     if ($request->ajax()) {
      
      $records = TrackingEventMaster::orderBy('id', 'DESC')->get();
        foreach($records as $record){
          return DataTables::of($records)
              ->addColumn('action', function ($record) {
                  $action = '<div class="d-flex pl-2 event-master-btn "><a href="/shipntrack/event-master/edit/' . $record->id . ' " class=" btn btn-success btn-sm "><i class="fas fa-edit"></i> Edit </a>';
                  $action .= '<div class="d-flex pl-2 event-master-btn "><a href="/shipntrack/event-master/delete/' . $record->id . ' " class=" btn btn-danger btn-sm "><i class="fas fa-remove"></i> Remove </a>';
                  return $action;
              })
              ->rawColumns(['action'])
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
      return view('shipntrack.EventMaster.edit', compact('records'));
      
   }

   public function EventMasterUpdate(Request $request, $id)
   {
      $validated = $request->validate([
        'event_code' => 'required',
        'description' => 'required',
        'status' => 'required',
      ]);
      $record = TrackingEventMaster::find($id);
      $record->event_code = $request->event_code;
      $record->description = $request->description;
      $record->active = $request->status;
      $record->update();
      
      return redirect()->intended('/shipntrack/event-master')->with('success', 'File update successfully!');
   }
}
