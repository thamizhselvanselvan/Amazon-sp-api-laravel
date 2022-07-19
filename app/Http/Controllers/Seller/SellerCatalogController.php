<?php

namespace App\Http\Controllers\Seller;

use invoice;
use ZipArchive;
use RedBeanPHP\R;
use League\Csv\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\SP_API\API\Catalog;
use Illuminate\Support\Facades\Auth;
use App\Models\seller\AsinMasterSeller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Models\seller\SellerAsinDetails;
use Yajra\DataTables\Facades\DataTables;

class SellerCatalogController extends Controller
{
  private $writer;

  public function index(Request $request)
  {

    return view('seller.Catalog.index');
  }

  public function ImportCatalogDetails()
  {
    $login_user = Auth::user();
    $seller_id = $login_user->bb_seller_id;
    if ($seller_id == "") {
      $seller_id = $login_user->id;
    }

    if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

      $base_path = base_path();
      $command = "cd $base_path && php artisan pms:seller-catalog-import $seller_id > /dev/null &";
      exec($command);
    } else {

      Log::info($seller_id);
      Artisan::call('pms:seller-catalog-import ' . $seller_id);
    }
    Log::alert("working on click");
  }

  public function catalogExport()
  {
    $user = Auth::user();
    $id = $user->bb_seller_id;
    if ($id == NULL) {
      $id = $user->id;
    }
    $id = 20;
    $user_name = $user->email;

    if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

      $base_path = base_path();
      $command = "cd $base_path && php artisan pms:seller-catalog-csv-export $user_name $id > /dev/null &";
      exec($command);
    } else {
      // Log::info($seller_id);
      Artisan::call('pms:seller-catalog-csv-export ' . $user_name . ' ' . $id);
    }
  }

  public function catalogDownload()
  {
    $user = Auth::user();
    $id = $user->bb_seller_id;
    if ($id == NULL) {
      $id = $user->id;
    }
    $id = 20;
    $user_name = $user->email;
    $zip = new ZipArchive;

    $exportFilePath = "excel/downloads/seller/" . $user_name . "/catalog";
    $fileName = Storage::path($exportFilePath . '/catalog.zip');
    if (!Storage::exists($exportFilePath . '/catalog.zip')) {
      Storage::put($exportFilePath . '.catalog.zip', '');
    }
    if ($zip->open($fileName, ZipArchive::CREATE) === TRUE) {
      $path = Storage::path($exportFilePath);
      $files = (scandir($path));
      foreach ($files as $key => $file) {
        if ($key > 1) {

          $path_csv = $path . '/' . $file;
          $relativeNameInZipFile = basename($path_csv);
          $zip->addFile($path_csv, $relativeNameInZipFile);
        }
      }

      $zip->close();
    }
    return response()->download($fileName);
  }

  public function Pricing(Request $request)
  {
    if ($request->ajax()) {

      $user = Auth::user();
      $seller_id = $user->bb_seller_id ? $user->bb_seller_id : $user->id;

      $data = SellerAsinDetails::query()->where('seller_id', $seller_id)->get();

      return DataTables::of($data)
        ->addIndexColumn()
        ->make(true);
    }
    return view('seller.catalog.pricing');
  }

  public function ExportPricing()
  {
    $user = Auth::user();
    $seller_id = $user->bb_seller_id ? $user->bb_seller_id : $user->id;

    commandExecFunc("mosh:seller-asin-price-export --seller_id=${seller_id}"); 
  }
}
