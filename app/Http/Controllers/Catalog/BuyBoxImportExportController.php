<?php

namespace App\Http\Controllers\Catalog;

use Illuminate\Http\Request;
use App\Services\BB\PushAsin;
use App\Models\FileManagement;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BuyBoxImportExportController extends Controller
{
    public function index()
    {
        return view('Catalog.Buybox.index');
    }
    public function BuyBoxUploadFile(Request $request)
    {
        if ($request->form_type == 'text_area') {
            $request->validate([
                'text_area'     => 'required',
                'destination'   => 'required',
                'priority'      => 'required|in:1,2,3,4',
            ]);

            $value = $request->text_area;
            $destinations = $request->destination;
            $priority = $request->priority;

            foreach ($destinations as $destination) {
                $asins = preg_split('/[\r\n| |:|,]/', $value, -1, PREG_SPLIT_NO_EMPTY);
                $country_code = buyboxCountrycode();

                foreach ($asins as $asin) {
                    $product[] = [
                        'seller_id' => $country_code[$destination],
                        'active'   =>  1,
                        'asin1' => $asin,
                    ];

                    $product_lowest_price[] = [
                        'asin'          => $asin,
                        'cyclic'        => 0,
                        'delist'        => 0,
                        'available'     => 0,
                        'priority'      => $priority,
                        'import_type'   => 'Seller',
                    ];
                }
                $push_to_bb = new PushAsin();
                $push_to_bb->PushAsinToBBTable(product: $product, product_lowest_price: $product_lowest_price, country_code: $destination, priority: $priority);
                $product = [];
                $product_lowest_price = [];
            }
        } elseif ($request->form_type == 'file_upload') {

            $user_id = Auth::user()->id;
            $priority = $request->priority;
            $destination = implode(',', $request->destination);

            $validation = $request->validate([
                'asin'          => 'required|mimes:txt,csv',
                'priority'      => 'required|in:1,2,3,4',
                'destination'   => 'required',
            ]);

            if (!$validation) {
                return back()->with('error', "Please upload file to import it to the database");
            }

            $import_file_time = date('Y-m-d-H-i-s');
            $file = file_get_contents($request->asin);
            $path = "AsinBuyBox/asin${import_file_time}.csv";
            Storage::put($path, $file);

            $file = $request->asin;
            $file_name = $file->getClientOriginalName();

            $file_info = [
                'user_id' => $user_id,
                'type' => 'IMPORT_ASIN_INTO_BUYBOX',
                'module' => "ASIN_BUYBOX_${destination}_${priority}",
                'file_name' => $file_name,
                'file_path' => $path,
                'command_name' => 'mosh:buybox-import-asin',

            ];

            FileManagement::create($file_info);
            fileManagement();
        }
        return redirect('catalog/buybox/import')->with('success', 'File has been uploaded successfully');
    }
    public function BuyBoxFileManagementMonitor(Request $request)
    {
        $type = $request->module_type;
        $file_check = fileManagementMonitoringNew($type);
        return response()->json($file_check);
    }

    public function ExportIndex()
    {
        return view('Catalog.Buybox.exportIndex');
    }

    public function ExportBuyBox(Request $request)
    {
        $user_id = Auth::user()->id;
        $file_path = 'BuyBoxExport/AsinForbb.csv';
        if ($request->export_type == 'text_area') {

            $request->validate([
                'priority' => 'required|in:1,2,3,4',
                'source' => 'required|in:IN,US,AE',
                'text_area' => 'required',
            ]);
            $priority = $request->priority;
            $country_code = $request->source;
            $textAreaData = $request->text_area;
            $asins = preg_split('/[\r\n| |:|,]/', $textAreaData, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($asins as $asin) {
                $records[] = ['ASIN' => $asin];
            }
            CSV_Write($file_path, ['ASIN'], $records);

            $file_info = [
                "user_id"       => $user_id,
                "type"          => "BUYBOX_EXPORT",
                "module"        => "BUYBOX_EXPORT_${country_code}_${priority}",
                "file_path"     => $file_path,
                "command_name"  => "mosh:buybox-export-asin"
            ];
            FileManagement::create($file_info);
            fileManagement();
        } elseif ($request->export_type == 'via_priority') {

            $request->validate([
                'priority' => 'required|in:1,2,3,4',
                'source' => 'required|in:IN,US,AE',
            ]);

            $priority = $request->priority;
            $country_code = $request->source;

            $file_info = [
                "user_id"        => $user_id,
                "type"           => "BUYBOX_EXPORT",
                "module"         => "BUYBOX_EXPORT_${country_code}_${priority}",
                "file_path"      => $file_path,
                "command_name"   => "mosh:buybox-export-asin"
            ];
            FileManagement::create($file_info);
            fileManagement();
        }
        return redirect('catalog/buybox/export')->with("success", "BuyBox data is exporting..");
    }
}
