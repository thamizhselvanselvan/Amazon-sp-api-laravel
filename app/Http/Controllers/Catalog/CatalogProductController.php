<?php

namespace App\Http\Controllers\Catalog;

use file;
use config;
use App\Models\Mws_region;
use Illuminate\Http\Request;
use App\Models\Aws_credential;
use App\Models\Catalog\catalog;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_master;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Services\SP_API\Config\ConfigTrait;
use SellingPartnerApi\Api\CatalogItemsV0Api;

class CatalogProductController extends Controller
{
    
    public function Index(Request $request)
    {
        $sources = Asin_master::select('source')->groupBy('source')->get();
        
        if ($request->ajax()) {
            $country_code = strtolower($request->country_code);
            $Tables = 'catalog'.$country_code.'s';
            $data = DB::connection('catalog')->select("SELECT * FROM $Tables ");

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('asin', function ($row){
                    
                    return '<a href="https://www.amazon.com/dp/'.$row->asin.'" target="_blank">'.$row->asin.'</a>';
                })
                ->editColumn('item_dimensions', function ($row) {
                    $dimension = 'NA';
                    $data = json_decode($row->item_dimensions);

                    if (isset($data->Height)) {
                        $dimension = '<p class="m-0 p-0">Height: ' . $data->Height->value . ' ' . $data->Height->Units . '</p>';
                    }
                    if (isset($data->Length)) {
                        $dimension .= '<p class="m-0 p-0">Length: ' . $data->Length->value . ' ' . $data->Length->Units . '</p>';
                    }
                    if (isset($data->Width)) {
                        $dimension .= '<p class="m-0 p-0">Width: ' . $data->Width->value . ' ' . $data->Width->Units . '</p>';
                    }

                    return $dimension;
                })
                ->editColumn('amount', function ($row) {
                    $amount = 'NA';
                    $amount = json_decode($row->list_price);
                    if(isset($amount)){
                        $amount = "<p>".$amount->CurrencyCode."&nbsp;".$amount->Amount."</p>";
                    }
                    return $amount;
                })
                ->addColumn('weight', function ($row) {

                    $data = json_decode($row->item_dimensions);
                    if (isset($data->Weight)) {
                        $dimension = '<p class="m-0 p-0">Weight: ' . $data->Weight->value . ' ' . $data->Weight->Units . '</p>';
                    } else {
                        $dimension = 'NA';
                    }
                    return $dimension;
                })
                ->rawColumns(['amount', 'item_dimensions', 'weight','asin'])
                ->make(true);
        }

        return view('Catalog.product.index', compact('sources'));
    }
    
    public function Amazon()
    {
        $asins = Asin_master::where('status', '=', 0)->get(['asin','source','user_id']);

        $count = 0;
        $asin_source = [];
        $class= 'catalog\\AmazonCatalogImport';
        foreach($asins as $asin){
            
            if($count == 10)
            {
                jobDispatchFunc($class, $asin_source, 'default');
                $asin_source = [];
                $count = 0;
            }
            else{
                
                $asin_source[] = [
                    'asin' => $asin->asin,
                    'source' => $asin->source,
                    'seller_id' => $asin->user_id
                ];
                $count ++;
            }
           
        }
        jobDispatchFunc($class, $asin_source, 'default');
        
        return redirect('catalog/product');
    }

    public function ExportCatalog(Request $request)
    {
        $country_code = $request->country_code;
        
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && php artisan mosh:catalog-export-csv $country_code> /dev/null &";
            exec($command);

            Log::warning("Export catalog command executed production  !!!");
        } else {

            Log::warning("Export catalog command executed local !");
            Artisan::call('mosh:catalog-export-csv'.' '. $country_code);
        }

        return redirect()->intended('/catalog/product');
    }

    public function DownloadCatalogIntocsv(Request $request)
    {
        // $files = glob(Storage::path('catalog/downloads/*'));
        // foreach ($files as $file) {
        //     if (is_file($file)) {
        //         unlink($file);
        //     }
        // }
        
        // $file_path =Storage::path("catalog/downloads/zip/Catalog".$request->country_code.".zip");
        $file_path =Storage::path("catalog/downloads/zip/CatalogIN.zip");
  
        return Storage::download($file_path);
        
       
    }

}
