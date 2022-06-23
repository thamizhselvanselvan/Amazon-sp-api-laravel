<?php

namespace App\Http\Controllers\Seller;

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
    $column_details = DB::connection('catalog')->select('DESCRIBE catalog');
    $column_name = [];
    foreach ($column_details as $key => $column_value) {
      if ($column_value->Field != 'seller_id' && $column_value->Field != 'id')
        $column_name[] = $column_value->Field;
    }
    $count = DB::connection('catalog')->select("SELECT count(asin) as count from catalog where seller_id = $id");
    $total_count = ($count[0]->count);
    $current_chunk = 0;
    $record_per_csv = 100000; //10 L
    $chunk = 10000; // 1 L
    $offset = 0;
    $count = 1;
    $fileNameOffset = 1;
    $user = '';
    $exportFilePath = "excel/downloads/seller/" . $user_name . "/catalog/catalog";
    $headers = [];
    $check = $record_per_csv / $chunk;
  
    while ($current_chunk <= $total_count) {

      $records = DB::connection('catalog')->select("SELECT *, NULL AS seller_id from catalog where seller_id = $id limit $offset, $chunk");

      if($count == 1) {
        if (!Storage::exists($exportFilePath . $fileNameOffset . '.csv')) {
          Storage::put($exportFilePath . $fileNameOffset . '.csv', '');
        }
        $this->writer = Writer::createFromPath(Storage::path($exportFilePath . $fileNameOffset . '.csv'), "w");
        $this->writer->insertOne($column_name);
      }

      $record = array_map(function ($datas) {
        $dat = [];
        foreach ($datas as $key => $data) {

          if ($key != 'id' && $key != 'seller_id') {
            $dat[] = $data;
          }
        }
        return (array) $dat;
      }, $records);

      $this->writer->insertall((array)$record);

      if ($check == $count) {
        $fileNameOffset++;
        $count = 1;
      } else {
        ++$count;
      }

      //pusher part
      $offset += $chunk;
      $current_chunk = $offset;
    }
  }
}
