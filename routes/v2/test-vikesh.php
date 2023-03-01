<?php

use RedBeanPHP\R;
use Carbon\Carbon;
use League\Csv\Reader;
use League\Csv\Writer;
use Carbon\CarbonPeriod;
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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Services\Catalog\PriceConversion;
use JeroenNoten\LaravelAdminLte\View\Components\Tool\Modal;

Route::get('zoho/index', 'VikeshTestController@index');
Route::get('zoho/test', 'VikeshTestController@ReadZohoTextFile');

Route::get('zoho/dump', function () {
    $token = json_decode(Storage::get("zoho/access_token.txt"), true)["access_token"];

    $payload = [
        "query" => [
            "module" => "Leads",
            "page" => 1
        ]
    ];
    $url = "https://www.zohoapis.com/crm/bulk/v2/read";

    $headers = Http::withoutVerifying()->withHeaders([
        "Authorization" => "Zoho-oauthtoken " . $token,
        "Content-Type" => "application/json"
    ])->post($url, $payload);

    $response = $headers->json();
    if (!Storage::exists('ZohoResponse/zoho-response1.txt')) {
        Storage::put('ZohoResponse/zoho-response1.txt', json_encode($response));
    }
    po($response);
});

Route::get('zoho/dump2', function () {
    $token = json_decode(Storage::get("zoho/access_token.txt"), true)["access_token"];
    $url = "https://www.zohoapis.com/crm/bulk/v2/read";

    $zohoResponse =  json_decode(Storage::get('ZohoResponse/zoho-response1.txt', true));
    $requestId = $zohoResponse->data[0]->details->id;

    $requestResponse = Http::withoutVerifying()->withHeaders([
        "Authorization" => "Zoho-oauthtoken " . $token
    ])->get($url . "/" . $requestId);

    po($requestResponse->json());
    Storage::put('ZohoResponse/zoho-response2.txt', json_encode($requestResponse->json()));
    po($requestId);
});

Route::get('zoho/dump3', function () {
    $file = Storage::get('zohocsv/1929333000107209152.csv');
    $host = config('database.connections.web.host');
    $dbname = config('database.connections.catalog.database');
    $port = config('database.connections.web.port');
    $username = config('database.connections.web.username');
    $password = config('database.connections.web.password');

    R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

    // $data = CSV_Reader('zohocsv/1929333000107209152.csv');
    $data = Reader::createFromPath(Storage::path('zohocsv/1929333000107209152.csv'), 'r');
    $data->setDelimiter(',');
    $data->setHeaderOffset(0);
    // $headers = $data->fetchOne();
    // po($headers);
    // exit;
    $column1 = [
        'owner',
        'company',
        'first_name',
        'last_name',
        'designation',
        'email',
        'phone',
        'fax',
        'mobile',
        'website',
        'lead_source',
        'lead_status',
        'industry',
        'no_of_employees',
        'annual_revenue',
        'rating',
        'campaign_source',
        'created_by',
        'modified_by',
        'created_time',
        'modified_time',
        'full_name',
        'street',
        'city',
        'state',
        'zip_code',
        'country',
        'description',
        'skype_id',
        'email_opt_out',
        'salutation',
        'secondary_email',
        'currency',
        'exchange_rate',
        'last_activity_time',
        'twitter',
        'layout',
        'order_number',
        'follow_up_status',
        'address',
        'product_code',
        'us_shipper',
        'product_cost',
        'quantity',
        'weight_in_lbs',
        'procurement_url',
        'nature',
        'product_category',
        'product_link',
        'asin',
        'sku',
        'purchased_by',
        'procurement_weight',
        'us_tracking_number',
        // 'bombino_shipping_weight_lbs',
        'india_shipping_weight_gr',
        'us_shipping_date',
        'purchase_date',
        'service_charges',
        'seller_commission',
        'payment_reference_number',
        'local_shipping',
        'payment_date',
        'paid_by',
        'amount_paid_by_customer',
        'vat',
        'refund',
        // 'refund_date',
        'adjustment_against_order',
        // 'refund_amount',
        'alternate_order_no',
        'order_creation_date',
        'delivered_to_customer',
        'us_edd',
        'bombino_shipment_id',
        'bombino_shipping_date',
        'purchase_reference_number',
        'seller_name',
        'card_used',
        'procured_from',
        'us_courier_name',
        'india_courier_name',
        'inventory_allocation_id',
        'india_tracking_number',
        'india_shipping_date',
        'width',
        'shipping_weight_in_grms',
        'bredth',
        'length',
        'us_edd1',
        'reverse_pickup_token_id',
        'marketplace_s_tax',
        'sabs_invoice_id',
        'gift_card_in',
        'cc_in',
        'formula_2',
        'exchange',
        'product_cost_inr',
        'international_shipping_inr',
        'duty_taxes_inr'
    ];
    $columns2 = [
        'owner',
        'alternate_order_no',
        'fulfilment_channel',
        'payment_reference_number1',
        'item_type',
        'score',
        'positive_score',
        'negative_score',
        'positive_touch_point_score',
        'touch_point_score',
        'negative_touch_point_score',
        'h_code',
        'tag',
        'refunded_by_us_seller',
        'record_image',
        'gstn',
        'customer_type1',
        'last_action',
        'last_sent_time',
        'last_action_time',
        'user_modified_time',
        'system_related_activity_time',
        'user_related_activity_time',
        'system_modified_time',
        'commission_on_can_ref',
        'refund_reference_number',
        'cc_charge_date',
        'cc_reference_number',
        'converted_date_time',
        'record_approval_status',
        'is_record_duplicate',
        'rto_rma_date',
        'first_visited_time',
        'visitor_score',
        'referrer',
        'average_time_spent_minutes',
        'last_visited_time',
        'first_visited_url',
        'number_of_chats',
        'days_visited',
        'lead_conversion_time',
        'international_shipment_id',
        'inventory_followup_status',
        'inventory_status',
        'us_seller_refund_date',
        'unsubscribed_mode',
        'unsubscribed_time',
        'converted_account',
        'converted_contact',
        'converted_deal',
        'territories',
        't_claim_amount',
        't_claim_status',
        't_claim_follow_up_status',
        't_claim_date',
        'step_down_inverter',
        'step_down_inventory_id',
        'international_courier_name',
        'india_courier_name1',
        'india_shipping_date1',
        'india_tracking_number1',
        'tcs_amount',
        'change_log_time_s',
        'mp_remitted_amount',
        'gst',
        'converted_s',
        'converted',
        'us_refund_source',
        'locked_s',
        'last_enriched_time_s',
        'enrich_status_s'
    ];
    $ignore = [' ', '__', '_id'];
    $zoho1 = R::dispense('zoho1');
    $zoho2 = R::dispense('zoho2');
    foreach ($data as  $record) {
        po($record);
        foreach ($record as $key => $value) {
            $headers = str_replace($ignore, '_', strtolower(trim($key)));

            if (in_array($headers, $column1)) {
                // po($value);
                if ($headers == 'refund_date') {
                    $zoho1->$headers = Carbon::parse($value)->toDateTimeString();
                }
                $zoho1->$headers = str_replace(' ', '', $value);
            }
            R::store($zoho1);

            // if (in_array($headers, $columns2)) {
            //     $zoho2->$headers = $value;
            //     // po($zoho);
            // }
            // po($headers);
            // po($zoho1);
        }
        // R::store($zoho2);

        // exit;
    }
    // po($data);
    exit;
    $zohoResponse =  json_decode(Storage::get('ZohoResponse/zoho-response2.txt', true));
    po($zohoResponse);
});

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
    // po(Carbon::parse('2022-12-09')->toDateTimeString());
    $date = Carbon::parse('2022-12-09')->toDateTimeString();
    po($date);
    exit;
    $date = Carbon::now()->addDays(105);
    $date1 = Carbon::now();
    if ($date <= $date1) {
        echo 'working';
    }
    po($date);
});
