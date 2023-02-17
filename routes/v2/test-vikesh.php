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
use Carbon\CarbonPeriod;
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

    $id = [];
    $previous_date = Carbon::now()->subDays(30)->toDateTimeString();
    $file_management_ids = FileManagement::where('created_at', '<', $previous_date)->get()->toArray();
    foreach ($file_management_ids as $file_management_id) {
        $id[] = $file_management_id['id'];
    }
    FileManagement::whereIn('id', $id)->delete();
    po($previous_date);
    po($id);
    exit;
    $date = Carbon::now()->addDays(105);
    $date1 = Carbon::now();
    if ($date <= $date1) {
        echo 'working';
    }
    po($date);
});
