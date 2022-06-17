<?php

namespace App\Http\Controllers\otherProduct;

use R;
use League\Csv\Reader;
use League\Csv\Writer;
use Illuminate\Http\Request;
use App\Models\OthercatDetails;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Models\otherCatalog\OtherCatalogAsin;

class anotherAmazonProductController extends Controller
{
    private $offset = 0;
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = OthercatDetails::query()->limit(1);

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
        return view('amazonOtherProduct.index');
    }

    public function exportOtherProduct(Request $request)
    {
        $selected_header = $request->input('selected');

        $selected_header =  $selected_header;
        // Log::alert($selected_header);
        $user = Auth::user()->email;
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            // exec('nohup php artisan pms:textiles-import  > /dev/null &');

            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:export-other-amazon $selected_header $user > /dev/null &";
            exec($command);

            // Log::warning("Export asin command executed production  !!!");
        } else {

            // Log::warning("Export asin command executed local !");
            Artisan::call('pms:export-other-amazon ' . $selected_header . ' ' . $user);
        }
    }

    public function other_file_download()
    {
        $user = Auth::user()->email;
        $path = "app/excel/downloads/otheramazon/" . $user;
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
        $user = Auth::user()->email;
        $file_path = "excel/downloads/otheramazon/" . $user . '/' . $id;
        if (Storage::exists($file_path)) {
            return Storage::download($file_path);
        }
        return 'file not exist';
    }

    public function asinUpload()
    {
        return view('amazonOtherProduct.asin_upload');
    }

    public function asinSave(Request $request)
    {
        $data = $request->textarea;
        $this->insertCatalogAsin($data);  
        return redirect()->intended('/other-product/amazon_com')->with('success', 'Asin Updated Successfully');
    }

    public function asinTxtSave(Request $request)
    {
        $request->validate([
            'asin' => 'required|mimes:txt'
        ]);
        if (!$request->hasFile('asin')) {
            return back()->with('error', "Please upload file to import it to the database");
        }
        $file = $request->file('asin');
        $csv = Reader::createFromPath($file, 'r');
        $this->insertCatalogAsin($csv);
        
        return redirect()->intended('/other-product/amazon_com')->with('success', 'Asin Updated Successfully');
    }

    public function insertCatalogAsin($data)
    {
        $user = Auth::user()->id;
        OtherCatalogAsin::where('user_id', $user)->delete();
        $datas = preg_split('/[\r\n| |:|,]/', $data, -1, PREG_SPLIT_NO_EMPTY);
        $insert_data = [];
        foreach ($datas as $data) {
            $insert_data[] = [
                'user_id' => $user,
                'asin' => $data,
                'status' => 0
            ];
        }
        OtherCatalogAsin::insert($insert_data);
        return true;
    }

}
