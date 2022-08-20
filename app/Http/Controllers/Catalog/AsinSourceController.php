<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use App\Services\BB\PushAsin;
use Yajra\DataTables\DataTables;
use App\Models\Catalog\AsinSource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class AsinSourceController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = AsinSource::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="d-flex"><a href="edit-asin/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';

                    return $actionBtn;
                })
                ->make(true);
        }
        return view('Catalog.AsinSource.index');
    }

    public function addAsin()
    {
        return view('Catalog.AsinSource.addAsin');
    }

    public function editasin($id)
    {
        $asin = AsinSource::where('id', $id)->first();
        return view('Catalog.AsinSource.edit', compact('asin'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'asin' => 'required|min:4|max:25',
            'source' => 'required|min:2|max:15',
        ]);

        $validated['source'] = strtoupper($validated['source']);

        AsinSource::where('id', $id)->update($validated);

        return redirect()->intended('/catalog/asin-source')->with('success', 'Asin has been updated successfully');
    }

    public function trash(Request $request)
    {
        AsinSource::where('id', $request->id)->delete();
        return redirect()->intended('/catalog/asin-source')->with('success', 'Asin has been pushed to Bin successfully');
    }

    public function trashView(Request $request)
    {
        $asins = AsinSource::onlyTrashed()->get();
        if ($request->ajax()) {
            return DataTables::of($asins)
                ->addIndexColumn()
                ->addColumn('action', function ($asins) {
                    return '<button data-id="' . $asins->id . '" class="restore btn btn-success"><i class="fas fa-trash-restore"></i> Restore</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('Catalog.AsinSource.trash');
    }

    public function restore(Request $request)
    {
        AsinSource::where('id', $request->id)->restore();
        return response()->json(['success' => 'Asin has restored successfully']);
    }


    public function importBulkAsin()
    {
        return view('Catalog.AsinSource.importAsin');
    }

    public function addBulkAsin(Request $request)
    {
        
        if($request->form_type == 'text_area')
        {
            $validate = $request->validate([
                'text_area' => 'required',
                'source'    =>  'required',
            ]);

            $user_id = Auth::user()->id;
            $record = $request->text_area;
            $source = $request->source;
            $asins = preg_split('/[\r\n| |:|,|.]/', $record, -1, PREG_SPLIT_NO_EMPTY);
            $country_code = buyboxCountrycode();

            if($source == 'UK'){
                return redirect('catalog/import-bulk-asin')->with('error', 'Seller not available');
            }
            foreach($asins as $asin_details)
            {
                $allData [] = [
                    'asin'  =>  $asin_details,
                    'user_id'   =>  $user_id,
                    'source'    =>  $source,
                ];

                $product [] = [
                    'seller_id' => $country_code[$source],
                    'active'   =>  1,
                    'asin1' => $asin_details,
                ];

                $product_lowest_price [] = [
                    'asin'  => $asin_details,
                    'import_type'   => 'Seller'
                ];
            }

            AsinSource::upsert($allData,['user_asin_source_unique'], ['asin']);
            $push_to_bb = new PushAsin();
            $push_to_bb->PushAsinToBBTable(product: $product, product_lowest_price: $product_lowest_price, country_code: $source);
        }
        elseif($request->form_type == 'file_upload')
        {
            $user_id = Auth::user()->id;
            
            $request->validate([
                'asin' => 'required|mimes:csv'
            ]);
    
            if (!$request->hasFile('asin')) {
                return back()->with('error', "Please upload file to import it to the database");
            }
    
            $msg = "Asin import has been completed!";
    
            $source = file_get_contents($request->asin);
            $path = 'AsinMaster/asin.csv';
            Storage::put($path, $source);
    
            if (App::environment(['Production', 'Staging', 'production', 'staging'])) {
    
                Log::warning("asin production executed");
    
                $base_path = base_path();
                $command = "cd $base_path && php artisan pms:asin-import ${user_id} > /dev/null &";
                exec($command);
                Log::warning("asin production command executed");
            } else {
    
                Log::warning("Export coma executed local !");
                Artisan::call('pms:asin-import' . ' ' . $user_id);
            } 
        }
        return redirect('catalog/import-bulk-asin')->with('success', 'All Asins uploaded successfully');
    }

    public function exportAsinToCSV()
    {
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            // exec('nohup php artisan pms:textiles-import  > /dev/null &');
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:asin-export > /dev/null &";
            exec($command);

            Log::warning("Export asin command executed production  !!!");
        } else {

            Log::warning("Export asin command executed local !");
            Artisan::call('pms:asin-export');
        }
        return redirect()->intended('/catalog/asin-source');
    }

    public function download_asin_master()
    {
        $file_path = "excel/downloads/asins/zip/CatalogAsin.zip";
        if (Storage::exists($file_path)) {
            return Storage::download($file_path);
        }
        return 'file not exist';
    }

    public function AsinTemplateDownload()
    {
        $file_path = public_path('template/Catalog-Asin-Template.csv');
        return response()->download($file_path);
    }

    public function getExchangeRate()
    {
        $records = AsinSource::select('asin', 'source')->get();
        foreach ($records as $record) {
            $asin = $record->asin;
            $source = $record->source;
        }
    }
}