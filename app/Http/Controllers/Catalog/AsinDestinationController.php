<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use App\Services\BB\PushAsin;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Models\Catalog\Asin_destination;
use Yajra\DataTables\Facades\DataTables;

class AsinDestinationController extends Controller
{
    public function index(Request $request)
    {
        // if ($request->ajax()) {

        //     $data = AsinDestination::orderBy('id', 'DESC')->get();
        //     return DataTables::of($data)
        //         ->addIndexColumn()
        //         ->addColumn('action', function ($row) {
        //             $actionBtn = '<div class="d-flex"><a href="edit-asin-destination/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
        //             $actionBtn .= '<div class="d-flex pl-2 trash"><a href="trash-asin-destination/' . $row->id . ' " class=" btn btn-sm btn-danger "><i class="fa fa-trash"></i> Remove</a>';

        //             return $actionBtn;
        //         })
        //         ->make(true);
        // }
        return view('Catalog.AsinDestination.index');
    }

    public function AsinDestinationImport()
    {
        return view('Catalog.AsinDestination.importAsin');
    }

    public function AsinDestinationFile(Request $request)
    {
        if ($request->form_type == 'text_area') {
            $request->validate([
                'text_area' => 'required',
                'destination'    => 'required',
                'priority'  => 'required',
            ]);
            $user_id = Auth::user()->id;
            $value = $request->text_area;
            $destinations = $request->destination;
            $priority = $request->priority;

            foreach ($destinations as $destination) {
                $asins = preg_split('/[\r\n| |:|,]/', $value, -1, PREG_SPLIT_NO_EMPTY);
                $country_code = buyboxCountrycode();
                // if ($destination == 'UK') {
                //     return redirect('catalog/import-asin-destination')->with('error', 'Seller not available!');
                // }
                foreach ($asins as $asin) {
                    $records[] = [
                        'asin'  => $asin,
                        'user_id'   => $user_id,
                        'priority'  => $priority,
                    ];

                    $product[] = [
                        'seller_id' => $country_code[$destination],
                        'active'   =>  1,
                        'asin1' => $asin,
                    ];

                    $product_lowest_price[] = [
                        'asin'  => $asin,
                        'import_type'   => 'Seller',
                        'priority'  => $priority,
                        'cyclic' => 0,
                    ];
                }

                $table_name = table_model_create(country_code: $destination, model: 'Asin_destination', table_name: 'asin_destination_');
                $table_name->upsert($records, ['user_asin_unique'], ['asin', 'priority']);
                $push_to_bb = new PushAsin();
                $push_to_bb->PushAsinToBBTable(product: $product, product_lowest_price: $product_lowest_price, country_code: $destination, priority: $priority);
                $records = [];
                $product = [];
                $product_lowest_price = [];
            }
        } elseif ($request->form_type == 'file_upload') {

            $user_id = Auth::user()->id;
            $priority = $request->priority;
            $destination = implode(',', $request->destination);
            $validation = $request->validate([
                'asin' => 'required|mimes:txt,csv',
                'destination' => 'required',
                'priority' => 'required',
            ]);

            if (!$validation) {
                return back()->with('error', "Please upload file to import it to the database");
            }

            $file = file_get_contents($request->asin);
            $path = 'AsinDestination/asin.csv';
            Storage::put($path, $file);

            commandExecFunc("mosh:Asin-destination-upload ${user_id} ${priority} --destination=${destination} ");
        }
        return redirect('catalog/import-asin-destination')->with('success', 'File has been uploaded successfully');
    }

    public function AsinDestinationEdit($id)
    {
        $asin = Asin_destination::find($id);
        return view('Catalog.AsinDestination.edit', compact('asin'));
    }

    public function AsinDestinationUpdate(Request $request, $id)
    {
        $update = $request->validate([
            'asin'  => 'required|min:2|max:25',
            'destination'   => 'required|min:2|max:25',
        ]);

        $update['destination'] = strtoupper($update['destination']);
        Asin_destination::where('id', $id)->update($update);
        return redirect()->intended('/catalog/asin-destination')->with('success', 'Asin has been updated successfully');
    }

    public function AsinDestinationTrash($id)
    {
        Asin_destination::where('id', $id)->delete();
        return redirect()->intended('/catalog/asin-destination')->with('success', 'Asin has been pushed to Bin successfully');
    }

    public function AsinDestinationTrashView(Request $request)
    {
        $asins = Asin_destination::onlyTrashed()->get();
        if ($request->ajax()) {

            $data = Asin_destination::orderBy('id', 'DESC')->get();
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
        Asin_destination::where('id', $id)->restore();
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
        if (Storage::exists($file)) {
            return Storage::download($file);
        }
        return 'File is not available right now!';
    }

    public function AsinDestinationBBTruncate(Request $request)
    {
        $validate = $request->validate([
            'destination' => 'required',
            'priority' => 'required',
        ]);
        $destinations = implode(',', $request->destination);
        $priority = $request->priority;
        commandExecFunc("mosh:Asin-destination-delete-priority-wise ${priority} --destinations=${destinations}");
        return redirect('catalog/asin-destination')->with('success', 'Table Truncate successfully');
    }

    public function AsinDestinationBBSearchDelete(Request $request)
    {

        $request->validate([
            'source'    =>  'required|in:IN,US',
            'priority'  =>  'required|in:1,2,3',
            'Asins'     =>  'required',
        ]);

        $source = strtolower($request->source);
        $priority = $request->priority;
        $Asins = array_unique(preg_split('/[\r\n| |:|,|.]/', $request->Asins, -1, PREG_SPLIT_NO_EMPTY));
        $asins = implode(',', $Asins);

        commandExecFunc("mosh:search-asin-delete-bb-destination ${priority} ${source} ${asins}");
        return redirect('/catalog/asin-destination')->with('success', 'ASINS has been deleted successfully!');
    }
}
