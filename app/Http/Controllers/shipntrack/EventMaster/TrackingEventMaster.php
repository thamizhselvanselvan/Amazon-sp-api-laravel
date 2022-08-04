<?php

namespace App\Http\Controllers\shipntrack\EventMaster;

use file;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class TrackingEventMaster extends Controller
{
   public function index()
   {
        return view('shipntrack.EventMaster.index');
   }

   public function upload()
   {
     return view('shipntrack.EventMaster.upload');
   }

   public function TrackingEventFileSave(Request $request)
   {    
        $validated = $request->validate([
            'tracking_event_csv' => ['required', 'mimes:csv', 'max:2048'],
        ]);
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
            $command = "cd $base_path && php artisan mosh:tracking-event-master > /dev/null &";
            exec($command);
        }else{
            Artisan::call('mosh:tracking-event-master');
        }
        
        return response()->json(['success' => 'File upload successfully']);
   }
}
