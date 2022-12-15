<?php

use RedBeanPHP\R;
use Carbon\Carbon;
use App\Models\User;
use League\Csv\Reader;
use App\Events\testEvent;
use App\Events\checkEvent;
use App\Models\Mws_region;
use Maatwebsite\Excel\Row;
use App\Jobs\TestQueueFail;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;
use App\Models\Aws_credential;
use App\Models\FileManagement;
use App\Services\Zoho\ZohoApi;
use Dflydev\DotAccessData\Data;
use SellingPartnerApi\Endpoint;
use App\Models\Inventory\Shelve;
use App\Services\Zoho\ZohoOrder;
use App\Models\Catalog\PricingIn;
use App\Models\Catalog\PricingUs;
use App\Models\Inventory\Country;
use App\Models\Universal_textile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Services\AWS_Nitshop\Index;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use App\Models\Admin\ErrorReporting;
use App\Models\Catalog\ExchangeRate;
use App\Services\SP_API\API\Catalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Route;
use App\Models\order\OrderItemDetails;
use App\Models\order\OrderUpdateDetail;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\TestController;
use App\Services\Inventory\ReportWeekly;
use Spatie\Permission\Models\Permission;
use phpDocumentor\Reflection\Types\Null_;
use SellingPartnerApi\Api\ProductPricingApi;
use App\Jobs\Seller\Seller_catalog_import_job;
use App\Models\ProcessManagement;
use Symfony\Component\Validator\Constraints\File;
use SellingPartnerApi\Api\CatalogItemsV20220401Api;
use App\Services\AWS_Business_API\Auth\AWS_Business;
use SellingPartnerApi\Api\FeedsV20210630Api as FeedsApi;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Month;
use App\Services\SP_API\API\AmazonOrderFeed\FeedOrderDetailsApp360;
use Illuminate\Validation\Rules\Exists;

// use ConfigTrait;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// use ConfigTrait;

Route::get('slack', function () {
    $process_manage = [
        'module'             => 'Catalog',
        'description'        => 'Amazon catalog import via queue',
        'command_name'       => 'mosh:catalog-amazon-import',
        'command_start_time' => now(),
    ];
    $id = ProcessManagement::create($process_manage)->toArray();
    po($id);

    exit;
    $slackMessage = "testing of slack";
    Log::info($slackMessage);
});


Route::get('t', function () {

    exit;

    exit;
    $test = [
        "408-0314297-2349941" => "33649360107739",
        "171-9103234-3571541" => "30433303327395",
        "407-5778559-6413925" => "18810620500419",
        "171-7536507-8431552" => "33955810336971",
        "403-4624716-2423502" => "15282683288923",
        "403-7100934-4302747" => "15561267157971",
        "407-8775072-4125935" => "54790673128779",
        "407-9710185-5629951" => "38475280357707",
        "408-3204418-8821906" => "10230083553475",
        "402-8497097-5407532" => "51286231797987",
        "405-5416641-0733142" => "67680090893059",
        "405-0606060-6345157" => "11258573010843",
        "171-3911337-3412300" => "45775846703947",
        "405-4504641-6186754" => "51906326582451",
        "403-9264745-6169105" => "11355882218203"
    ];

    foreach ($test as $ama => $ord) {
        $order_zoho = [
            'store_id' => 20,
            "amazon_order_id" => $ama,
            "order_item_id" => $ord,
            "created_at" => now(),
            "updated_at" => now()
        ];

        po($order_zoho);
        $order_response = OrderUpdateDetail::upsert(
            $order_zoho,
            [
                "amazon_order_id",
                "order_item_id"
            ],
            [
                "store_id",
                "amazon_order_id",
                "order_item_id",
                "created_at",
                "updated_at"
            ]
        );
    }
});

Route::get('zoho_update', function () {

    $idToWork = 377125000000428001;

    $zoho = new ZohoApi;

    //dd($zoho->getLead($idToWork));

    // dd($zoho->getLead($idToWork));
    // dd($zoho->search('403-4468830-9728365', '28528755520011'));
    dd($zoho->search('407-2228273-7615502', '59079216484499'));

    $arr = [
        "407-2228273-7615502" => "59079216484499",
        "407-0794244-6192302" => "29881457847227",
        "171-2796583-2501108" => "29750750050139",
        "405-1941943-2552327" => "31942521053411",
        "404-2792346-5143552" => "53586570383155",
        "407-9934192-1111537" => "33547584513475",
        "403-1299607-7100313" => "16394164032835",
        "404-9409576-4016363" => "43517550134755",
        "407-7006516-2944324" => "48664027424963",
        "403-1785735-6958763" => "26140193419787",
        "406-5992439-2069933" => "09242763051851",
        "403-3377666-6141140" => "15340397625619",
        "407-0708733-7604307" => "14627924587139",
        "407-8511247-3487562" => "44104461022051",
        "406-5661078-9535533" => "30225153154643",
        "407-3858010-0693960" => "51406269431643",
        "405-4388214-4041163" => "67431219091059",
        "405-8232255-0405169" => "26102888616043",
        "407-6957240-5980355" => "67252246932299",
        "404-3666084-3185928" => "20851893386883",
        "171-5469768-2474763" => "50245189619139",
        "408-9250439-2184334" => "07571427309379",
        "405-6538133-1037902" => "48800426945899",
        "402-1989061-2837109" => "39358517288931",
        "408-5600519-4580319" => "03275239200435"
    ];

    foreach ($arr as $ama => $ord) {

        $exists = $zoho->search($ama, $ord);

        if ($exists && array_key_exists('data', $exists) && array_key_exists(0, $exists['data']) && array_key_exists('Alternate_Order_No', $exists['data'][0])) {

            $order_zoho = [
                'store_id' => 20,
                "amazon_order_id" => $ama,
                "order_item_id" => $ord,
                "zoho_id" => $exists['data'][0]['id'],
                "zoho_status" => 1
            ];

            $order_response = OrderUpdateDetail::upsert(
                $order_zoho,
                [
                    "amazon_order_id",
                    "order_item_id"
                ],
                [
                    "zoho_id",
                    "store_id",
                    "zoho_status"
                ]
            );
        } else if (!$exists) {

            $zoho_order = new ZohoOrder;
            $data = $zoho_order->index($ama);

            po($data);

            // $order_zoho = [
            //     "store_id" => 20,
            //     "amazon_order_id" => $ama,
            //     "order_item_id" => $ord,
            //     "zoho_id" => $data[''],
            //     "zoho_status" => 1
            // ];

            // $order_response = OrderUpdateDetail::upsert(
            //     $order_zoho,
            //     [
            //         "amazon_order_id",
            //         "order_item_id"
            //     ],
            //     [
            //         "zoho_id",
            //         "store_id",
            //         "zoho_status"
            //     ]
            // );
        }
    }

    dd('all done');

    // dd($zoho->updateLead('377125000000430025', [
    //     "Amount_Paid_by_Customer" => "10"
    // ]));


    exit;
    $test = json_decode('{"data":[{"code":"SUCCESS","details":{"Modified_Time":"2022-11-11T18:13:51+05:30","Modified_By":{"name":"Mosh","id":"1929333000000097003"},"Created_Time":"2022-11-11T18:13:51+05:30","id":"1929333000099290066","Created_By":{"name":"Mosh","id":"1929333000000097003"}},"message":"record added","status":"success"}]}', true);

    if (array_key_exists('data', $test) && array_key_exists(0, $test['data']) && array_key_exists('code', $test['data'][0])) {
        //   /  dd($test['data'][0]['details']['id']);
    }

    dd($test);

    exit;

    // $zoho = new ZohoApi;
    // dd($zoho->getAccessToken());


    //
    // $robin = User::create([
    //     'name' => 'Robin Singh',
    //     'email' => 'cliqnshop@app360.io',
    //     'password' => Hash::make(123456),
    // ]);
    // $invoice = Role::create(['name' => 'Cliqnshop']);
    // $invoice_permission = Permission::create(['name' => 'Cliqnshop']);
    // $invoice->givePermissionTo($invoice_permission);
    // $robin->assignRole('Cliqnshop');


    // exit;

    $ZohoOrder = new ZohoOrder;

    dd($ZohoOrder->index());
});

Route::get('channel', function () {
    return view('checkChannel');
});

Route::get('job', function () {
    TestQueueFail::dispatch();
});

Route::get('deleterole', function () {
    $role = Role::findByName('Orders');
    $role->delete();
});

Route::get('rename', function () {
    $currenturl = request()->getSchemeAndHttpHost();
    return $currenturl;
});

Route::get('/', 'Auth\LoginController@showLoginForm')->name('/');
Auth::routes();

Route::match(['get'], 'login', 'Admin\HomeController@dashboard')->name('login');

Route::match(['get', 'post'], '/logout', function () {

    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('home', 'Admin\HomeController@dashboard')->name('home');
Route::resource('/tests', 'TestController');
Route::get('test/seller', 'TestController@SellerTest');
Route::get('/asin/{asin}/{code}', 'TestController@getASIN');


include_route_files(__DIR__ . '/pms/');
