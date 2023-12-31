<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Admin\BB\BB_Product;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\seller\AsinMasterSeller;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AsinMasterController extends Controller
{        //
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $user = Auth::user();
            $seller_id = $user->bb_seller_id ? $user->bb_seller_id : $user->id;

            $data = AsinMasterSeller::query()->where('seller_id', $seller_id)->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->make(true);
        }

        return view('seller.asin_master.index');
    }

    public function addAsin()
    {
        return view('AsinMaster.addAsin');
    }

    public function editasin($id)
    {
        $asin = AsinMasterSeller::where('id', $id)->first();
        return view('AsinMaster.edit', compact('asin'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'asin' => 'required|min:4|max:25',
            'source' => 'required|min:2|max:15'
        ]);

        $validated['source'] = strtoupper($validated['source']);

        AsinMasterSeller::where('id', $id)->update($validated);

        return redirect()->intended('/seller/asin-master')->with('success', 'Asin has been updated successfully');
    }


    public function trash(Request $request)
    {
        $user = Auth::user();
        $seller_id = $user->bb_seller_id;

        if (!$seller_id) {
            $seller_id = $user->id;
        }

        $asins = AsinMasterSeller::where('id', $request->id)->get();
        $asin = $asins[0]->asin;
        $country_code = $asins[0]->source;

        AsinMasterSeller::where('id', $request->id)->delete();

        $bb_product = table_model_set($country_code, 'BB_Product', 'product');

        $bb_product->where('asin1', $asin)->where('seller_id', $seller_id)->delete();

        return redirect('/seller/asin-master')->with('success', 'Asin has been pushed to Bin successfully');
    }

    public function trashView(Request $request)
    {
        if ($request->ajax()) {
            $asins = AsinMasterSeller::onlyTrashed()->get();

            return DataTables::of($asins)
                ->addIndexColumn()

                ->addColumn('action', function ($asins) {
                    return '<button data-id="' . $asins->id . '" class="restore btn btn-success"><i class="fas fa-trash-restore"></i> Restore</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('AsinMaster.trash');
    }

    public function restore(Request $request)
    {
        AsinMasterSeller::where('id', $request->id)->restore();
        return response()->json(['success' => 'Asin has restored successfully']);
    }


    public function importBulkAsin()
    {
        return view('seller.asin_master.importAsin');
    }

    public function addBulkAsin(Request $request)
    {
        $request->validate([
            'asin' => 'required|mimes:csv,txt,xls,xlsx'
        ]);
        if (!$request->hasFile('asin')) {
            return back()->with('error', "Please upload file to import it to the database");
        }

        $msg = "Asin import has been completed!";

        $source = file_get_contents($request->asin);
        $path = 'Seller/AsinMaster/asin.csv';

        Storage::put($path, $source);

        $user = Auth::user();
        $seller_id = $user->bb_seller_id;

        if (!$seller_id) {
            $seller_id = $user->id;
        }

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:seller-asin-import $seller_id > /dev/null &";
            exec($command);
        } else {

            Artisan::call('pms:seller-asin-import ' . $seller_id);
        }

        return redirect('/seller/asin-master')->with('success', 'All Asins uploaded successfully');
    }

    public function deleteAsinView()
    {
        return view('seller.asin_master.deleteAsin');
    }

    public function SellerAsinRemove(Request $request)
    {
        $request->validate([
            'asin' => 'required|mimes:csv'
        ]);

        if (!$request->hasFile('asin')) {
            return back()->with('error', "Please upload file to import it to the database");
        }

        $source = file_get_contents($request->asin);
        $path = 'Seller/Remove/AsinMaster/remove_asin.csv';

        Storage::put($path, $source);

        $user = Auth::user();
        $seller_id = $user->bb_seller_id;

        if (!$seller_id) {
            $seller_id = $user->id;
        }

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:seller-asin-remove $seller_id > /dev/null &";
            exec($command);
        } else {

            Artisan::call('pms:seller-asin-remove ' . $seller_id);
        }

        return redirect('/seller/import-bulk-asin')->with('success', 'Asin Deleted Successfully');
    }

    public function DownloadCSVTemplate()
    {
        $filepath = public_path('template/Seller-Asin-Destination-Template.csv');
        return Response()->download($filepath);
    }
}
