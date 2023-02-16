<?php

use Carbon\Carbon;
use League\Csv\Writer;
use App\Models\FileManagement;
use App\Models\Catalog\catalogae;
use App\Models\Catalog\catalogin;
use App\Models\Catalog\catalogsa;
use App\Models\Catalog\catalogus;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\Catalog\Catalog_in;
use App\Models\Catalog\Catalog_us;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\Asin_source;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\Catalog\PriceConversion;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Modal;

Route::get('export', function () {
    $chunk = 1000;
    $total =  DB::connection('catalog')->select("SELECT cat.asin 
    FROM asin_source_ins as source 
    RIGHT JOIN catalognewins as cat 
    ON cat.asin=source.asin 
    WHERE source.asin IS NULL
   ");

    $loop = ceil(count($total) / $chunk);
    for ($i = 0; $i < $loop; $i++) {

        $data =  DB::connection('catalog')->select("SELECT cat.asin 
        FROM asin_source_ins as source 
        RIGHT JOIN catalognewins as cat 
        ON cat.asin=source.asin 
        WHERE source.asin IS NULL
        LIMIT 1000");
        $asin = [];
        foreach ($data as $record) {
            $asin[] = [
                'asin' => $record->asin,
                'user_id' => '13',
                'status' => 0
            ];
        }
        $table = table_model_create(country_code: 'in', model: 'Asin_source', table_name: 'asin_source_');
        $table->upsert($asin, ['user_asin_unique'], ['asin', 'status']);
        Log::warning('successfully' . $i);
    }
});

Route::get('test', function () {
    $today = Carbon::today()->toDateTimeString();
    $now = Carbon::now()->toDateTimeString();
    po($today);
    // exit;
    // $data = FileManagement::where("module", "BUYBOX_EXPORT_IN_1")
    //     ->whereBetween('created_at', [$today, '2023-02-15 12:01:53'])
    //     ->count();

    // po($data);
    // exit;

    $user_id = 124;
    $priority = 1;
    $countryCode = ['1' => 'IN', '2' => 'US',  '3' => 'AE'];
    $key = 1;
    $file_info = [];
    // foreach ($countryCode as $country) {
    Query:
    // if ($key < 4) {

    $data = FileManagement::where("module", "BUYBOX_EXPORT_$countryCode[$key]_1")
        ->whereBetween('created_at', [$today, $now])
        ->count();
    if ($data == '0') {

        $file_info = [
            "user_id"        => $user_id,
            "type"           => "BUYBOX_EXPORT",
            "module"         => "BUYBOX_EXPORT_$countryCode[$key]_1",
            "command_name"   => "mosh:buybox-export-asin"
        ];
        FileManagement::create($file_info);
    } else {

        $key++;

        if ($key > 3) {
            exit;
        }

        goto Query;
    }
});
