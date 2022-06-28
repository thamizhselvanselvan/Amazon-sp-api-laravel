<?php

namespace App\Http\Controllers\otherProduct;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\OthercatDetailsIndia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Models\otherCatalog\OtherCatalogAsin;

class OtherAmazonInProductController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = OthercatDetailsIndia::query()->limit(1);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('availability', function ($row) {
                    return $row ? 'Available' : 'NA';
                })
                ->editColumn('hs_code_gst', function ($row) {
                    return $row->hs_code . " / " . $row->gst;
                })
                ->editColumn('price1_price_inr', function ($row) {
                    return $row->price1 . " / " . $row->price_inr;
                })

                ->editColumn('flipkart_amazon', function ($row) {
                    return $row->flipkart . " / " . $row->amazon;
                })

                ->editColumn('uae_latency_b2c_latency', function ($row) {
                    return $row->uae_latency . " / " . $row->b2c_latency;
                })

                ->editColumn('image_p_image_d', function ($row) {
                    return $row->image_p . " / " . $row->image_d;
                })

                ->editColumn('height_length_width', function ($row) {
                    return $row->height . " / " . $row->length . " / " . $row->width;
                })
                ->editColumn('model_mpn', function ($row) {
                    return $row->model . " / " . $row->mpn;
                })

                ->rawcolumns(['availability', 'image_p_image_d', 'hs_code_gst', 'price1_price_inr', 'uae_latency_b2c_latency', 'flipkart_amazon', 'height_length_width', 'model_mpn'])
                ->make(true);
        }
        return view('amazonOtherProduct.amazonOtherProductIndia.index');
    }

    public  function exportOtherProductIn(Request $request)
    {
        $selected_header = $request->input('selected');
        $type = $request->input('type');

        $selected_header =  $selected_header;
        $user = Auth::user();
        $id = $user->id;
        $email = $user->email;

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:export-other-amazon-in $selected_header $email $id $type> /dev/null &";
            exec($command);

            // Log::warning("Export asin command executed production  !!!");
        } else {

            // Log::warning("Export asin command executed local !");
            Artisan::call('pms:export-other-amazon-in ' . $selected_header . ' ' . $email . ' ' . $id . ' ' . $type);
        }
    }

    public function other_file_download_in()
    {
        $user = Auth::user()->email;
        $path = "app/excel/downloads/otheramazonIN/" . $user;
        $path = storage_path($path);
        $files = (scandir($path));

        $filesArray = [];
        foreach ($files as $key => $file) {
            if ($key > 1) {

                $filesArray[][$file] =  date("F d Y H:i:s.", filemtime($path . '/' . $file));
            }
        }

        return response()->json(['success' => true, "files_lists" => $filesArray]);
    }

    public function download_other_product($id)
    {
        //Other Amazon file download
        // $file_path = "excel/downloads/otheramazon/otherProductDetails".$id.'.csv';
        $user = Auth::user()->email;
        $file_path = "excel/downloads/otheramazonIN/" . $user . '/' . $id;
        //$path = Storage::path($file_path);
        if (Storage::exists($file_path)) {
            return Storage::download($file_path);
        }
        return 'file not exist';
    }




    public function asinUpload()
    {
        return view('amazonOtherProduct.amazonOtherProductIndia.asin_upload');
    }

    public function asinSave(Request $request)
    {
        $data = $request->textarea;

        $path = 'OtherAmazon/amazomdotin/Asin.txt';
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }

        storage::put($path, $data);
        $this->insertCatalogAsin();
        return redirect()->intended('/other-product/amazon_in')->with('success', 'Asin Updated Successfully');
    }

    public function asinTxtSave(Request $request)
    {
        $request->validate([
            'asin' => 'required|mimes:txt'
        ]);

        if (!$request->hasFile('asin')) {
            return back()->with('error', "Please upload file to import it to the database");
        }

        $source = file_get_contents($request->asin);

        $path = 'OtherAmazon/amazomdotin/Asin.txt';
        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }

        storage::put($path, $source);
        $this->insertCatalogAsin();

        return redirect()->intended('/other-product/amazon_in')->with('success', 'Asin Updated Successfully');
    }

    public function insertCatalogAsin()
    {
        $user = Auth::user()->id;
        $type = 'in';
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:other-catalog-asin-import $user $type > /dev/null &";
            exec($command);
        } else {

            Artisan::call('pms:other-catalog-asin-import ' . $user . ' ' . $type);
        }

        return true;
    }
}
