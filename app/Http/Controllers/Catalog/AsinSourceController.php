<?php
namespace App\Http\Controllers\Catalog;

use RedBeanPHP\R;
use App\Models\Catalog;
use Illuminate\Http\Request;
use App\Services\BB\PushAsin;
use Yajra\DataTables\DataTables;
use App\Models\Catalog\AsinSource;
use Illuminate\Support\Facades\DB;
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
        // if ($request->ajax()) {
        //     $data = AsinSource::query();
        //     return DataTables::of($data)
        //         ->addIndexColumn()
        //         ->addColumn('action', function ($row) {
        //             $actionBtn = '<div class="d-flex"><a href="edit-asin/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
        //             $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';

        //             return $actionBtn;
        //         })
        //         ->make(true);
        // }
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
    {   AsinSource::where('id', $request->id)->restore();
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
                'source'    =>  ['required'],
            ]);

            $source_key_exists = 0;
            $user_id = Auth::user()->id;
            $record = $request->text_area;
            $sources = $request->source;
            
            foreach($sources as $key => $source){
                $asins = preg_split('/[\r\n| |:|,|.]/', $record, -1, PREG_SPLIT_NO_EMPTY);
                $country_code = buyboxCountrycode();
                $check_table = DB::connection('catalog')->select('SHOW TABLES');
                foreach($check_table as $key => $table_name)
                {
                    foreach($table_name as $name_of_table)
                    {
                        if($name_of_table == 'catalognew'.strtolower($source).'s'){
                            $source_key_exists = 1;
                        }
                    }
                }
                if($source_key_exists == 0){
                    $redbean = new NewCatalog();
                    $redbean->RedBeanConnection();
                    $catalog_table = 'catalognew'.strtolower($source).'s';
                    $NewCatalogs = R::dispense($catalog_table);
                    $NewCatalogs->asin = '';
                    R::store($NewCatalogs);
                }

                foreach($asins as $asin_details)
                {
                    $allData [] = [
                        'asin'  =>  $asin_details,
                        'user_id'   =>  $user_id,
                    ];
                }
                $table_name = table_model_create(country_code:$source, model:'Asin_source', table_name:'asin_source_');
                $table_name->upsert($allData,['user_asin_unique'], ['asin']);
                $allData = [];
                commandExecFunc(" mosh:catalog-amazon-import ");
            }
           
        }
        elseif($request->form_type == 'file_upload')
        {
            $user_id = Auth::user()->id;
            $request->validate([
                'asin' => 'required|mimes:txt,csv'
            ]);
            if (!$request->hasFile('asin')) {
                return back()->with('error', "Please upload file to import it to the database");
            }
    
            $msg = "Asin import has been completed!";

            $source = $request->source;
            $source = implode(',', $source);
            $file = file_get_contents($request->asin);
            $path = 'AsinMaster/asin.csv';
            Storage::put($path, $file);
            commandExecFunc("pms:asin-import ${user_id} --source=${source} ");
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

    public function AsinTruncate(Request $request)
    {
        $sources = $request->source;
        foreach($sources as $source)
        {
            $country_code = strtolower($source);
            $table_name = table_model_create(country_code:$country_code, model:'Asin_source', table_name:'asin_source_');
            $table_name->truncate();
        }
        return redirect('catalog/asin-source')->with('success', 'Table Truncate successfully');
    }

    public function CatalogSearch(Request $request)
    {
        $request->validate([
            'source' => 'required|in:IN,US',
            'catalog_asins' => 'required',
        ]);
        $country_code = strtolower($request->source);
        $asins = array_unique(preg_split('/[\r\n| |:|,|.]/', $request->catalog_asins, -1, PREG_SPLIT_NO_EMPTY));
        $pricing = ($country_code == 'in') ? 'price.in_price, price.ind_to_uae, price.ind_to_sg, price.ind_to_sa' : ' us_price, usa_to_uae, usa_to_sg ' ;
        
        foreach($asins as $key => $asin)
        {
            $catalogs [] = DB::connection('catalog')->select("SELECT cat.asin, cat.seller_id, cat.source, cat.height, cat.length, cat.width, cat.unit, cat.weight, cat.weight_unit, cat.brand, cat.manufacturer,
            ${pricing}
            FROM catalognew${country_code}s  as cat
            JOIN pricing_${country_code}s as price
            ON cat.asin = price.asin
            where cat.asin = '$asin'
            ");
        
        }
        return response()->json([$catalogs]);
    }
}
