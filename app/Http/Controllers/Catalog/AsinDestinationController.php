<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Catalog\AsinDestination;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AsinDestinationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            
            $data = AsinDestination::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="d-flex"><a href="edit-asin-destination/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<div class="d-flex pl-2 trash"><a href="trash-asin-destination/' . $row->id . ' " class=" btn btn-sm btn-danger "><i class="fa fa-trash"></i> Remove</a>';

                    return $actionBtn;
                })
                ->make(true);
        }
        return view('Catalog.AsinDestination.index');
    }

    public function AsinDestinationImport()
    {
        return view('Catalog.AsinDestination.importAsin');
    }

    public function AsinDestinationFile(Request $request)
    {
        $user_id = Auth::user()->id;
        $request->validate([
            'asin' => 'required|mimes:csv',
        ]);

        if(!$request->hasFile('asin'))
        {
            return back()->with('error', "Please upload file to import it to the database");
        }
    
        $file = file_get_contents($request->asin);
        
        $path = 'AsinDestination/asin.csv';
        // if(!Storage::exists($path)){
            Storage::put($path, $file);
        // }

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            Log::warning("asin production executed");

            $base_path = base_path();
            $command = "cd $base_path && php artisan mosh:Asin-destination-upload ${user_id} > /dev/null &";
            exec($command);
            Log::warning("asin production command executed");
        } else {

            Log::warning("Export coma executed local !");
            Artisan::call('mosh:Asin-destination-upload' . ' ' . $user_id);
        }
        return redirect('catalog/import-asin-destination')->with('success', 'File has been uploaded successfully');
    }

    public function AsinDestinationEdit($id)
    {
        $asin = AsinDestination::find($id);
        return view('Catalog.AsinDestination.edit', compact('asin'));
    }

    public function AsinDestinationUpdate(Request $request, $id)
    {
        $update = $request->validate([
            'asin'  => 'required|min:2|max:25',
            'destination'   => 'required|min:2|max:25',
        ]);

        $update['destination'] = strtoupper($update['destination']);
        AsinDestination::where('id', $id)->update($update);
        return redirect()->intended('/catalog/asin-destination')->with('success', 'Asin has been updated successfully');
    }

    public function AsinDestinationTrash($id)
    {
        AsinDestination::where('id', $id)->delete();
        return redirect()->intended('/catalog/asin-destination')->with('success', 'Asin has been pushed to Bin successfully');
    }

    public function AsinDestinationTrashView(Request $request)
    {
        $asins = AsinDestination::onlyTrashed()->get();
        if ($request->ajax()) {
            
            $data = AsinDestination::orderBy('id', 'DESC')->get();
            return DataTables::of($asins)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="d-flex"><a href="restore/' . $row->id . '" class="restore btn btn-primary btn-sm"><i class="fas fa-trash-restore "></i> Restore</a>';
                    return $actionBtn;
                })
                ->make(true);
        }
        return view('Catalog.AsinDestination.bin');
    }

    public function AsinDestinationTrashRestore($id)
    {
        AsinDestination::where('id', $id)->restore();
        return redirect()->intended('/catalog/asin-destination/bin')->with('success', 'Asin has been restored successfully');
    }

    public function AsinDestinationDownloadTemplate()
    {
        $downloadFile = public_path('template/Catalog-asin-destination.csv');
        return response()->download($downloadFile);
    }

    public function AsinDestinationAsinExport(Request $request)
    {
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && php artisan mosh:asin-destination-csv-export > /dev/null &";
            exec($command);

            Log::warning("Export asin command executed production  !!!");
        } else {

            Log::warning("Export asin command executed local !");
            Artisan::call('mosh:asin-destination-csv-export');
        }
        return redirect()->intended('/catalog/asin-destination');
    }

    public function AsinDestinationDownloadCsvZip()
    {
        $file = 'excel/downloads/asin_destination/zip/CatalogAsinDestination.zip';
        if(Storage::exists($file)){
            return Storage::download($file);
        }
        return 'File is not available right now!';
    }
}
