<?php

namespace App\Http\Controllers\Catalog;

use RedBeanPHP\R;
use App\Models\Catalog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\BB\PushAsin;
use App\Models\FileManagement;
use Yajra\DataTables\DataTables;
use App\Models\Catalog\AsinSource;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_source;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Catalog\AsinSourcein;
use Illuminate\Support\Facades\Auth;
use App\Services\SP_API\API\NewCatalog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class AsinSourceController extends Controller
{
    public function index(Request $request)
    {
        return view('Catalog.AsinSource.index');
    }

    public function addAsin()
    {
        return view('Catalog.AsinSource.addAsin');
    }

    public function editasin($id)
    {
        $asin = Asin_source::where('id', $id)->first();
        return view('Catalog.AsinSource.edit', compact('asin'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'asin' => 'required|min:4|max:25',
            'source' => 'required|min:2|max:15',
        ]);
        $validated['source'] = strtoupper($validated['source']);
        Asin_source::where('id', $id)->update($validated);
        return redirect()->intended('/catalog/asin-source')->with('success', 'Asin has been updated successfully');
    }

    public function trash(Request $request)
    {
        Asin_source::where('id', $request->id)->delete();
        return redirect()->intended('/catalog/asin-source')->with('success', 'Asin has been pushed to Bin successfully');
    }

    public function trashView(Request $request)
    {
        $asins = Asin_source::onlyTrashed()->get();
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
        Asin_source::where('id', $request->id)->restore();
        return response()->json(['success' => 'Asin has restored successfully']);
    }

    public function importBulkAsin()
    {
        return view('Catalog.AsinSource.importAsin');
    }

    public function addBulkAsin(Request $request)
    {
        if ($request->form_type == 'text_area') {
            $request->validate([
                'text_area' => 'required',
                'source'    =>  ['required'],
            ]);

            $user_id = Auth::user()->id;
            $record = $request->text_area;
            $source = $request->source;

            // foreach ($sources as $key => $source) {
            $asins = preg_split('/[\r\n| |:|,|.]/', $record, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($asins as $asin_details) {
                $allData[] = [
                    'asin'  =>  $asin_details,
                    'user_id'   =>  $user_id,
                ];
            }
            $table_name = table_model_create(country_code: $source, model: 'Asin_source', table_name: 'asin_source_');
            $table_name->upsert($allData, ['user_asin_unique'], ['asin']);
            $allData = [];
            commandExecFunc(" mosh:catalog-amazon-import ");
            // }
        } elseif ($request->form_type == 'file_upload') {
            $user_id = Auth::user()->id;
            $request->validate([
                'source' => ['required'],
                'asin' => 'required|mimes:txt,csv'
            ]);
            if (!$request->hasFile('asin')) {
                return back()->with('error', "Please upload file to import it to the database");
            }

            $source = $request->source;
            // $source = implode(',', $source);
            $file = file_get_contents($request->asin);
            $import_file_time = date('Y-m-d-H-i-s');
            $path = "AsinSource/asin${import_file_time}.csv";
            Storage::put($path, $file);

            $file = $request->asin;
            $file_name = $file->getClientOriginalName();

            $file_info = [
                'user_id' => $user_id,
                'type' => 'IMPORT_ASIN_SOURCE',
                'module' => "ASIN_SOURCE_${source}",
                'file_name' => $file_name,
                'file_path' => $path,
                'command_name' => 'pms:asin-import',
            ];

            FileManagement::create($file_info);
            fileManagement();
        }
        return redirect('catalog/import-bulk-asin')->with('success', 'Asins file uploaded, checking file\'s data');
    }

    public function exportAsinToCSV()
    {
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            // exec('nohup php artisan pms:textiles-import  > /dev/null &');
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:asin-export > /dev/null &";
            exec($command);
        } else {
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

    public function AsinTruncate(Request $request)
    {
        $sources = $request->source;
        foreach ($sources as $source) {
            $country_code = strtolower($source);
            $table_name = table_model_create(country_code: $country_code, model: 'Asin_source', table_name: 'asin_source_');
            $table_name->truncate();
        }
        return redirect('catalog/asin-source')->with('success', 'Table Truncate successfully');
    }

    public function SourceFileManagementMonitor(Request $request)
    {
        $data = fileManagementMonitoringNew($request->module_type);
        return response()->json($data);
    }
}
