<?php

namespace App\Http\Controllers\orders;

use Exception;
use Carbon\Carbon;
use RedBeanPHP\R as R;
use AWS\CRT\HTTP\Response;
use Hamcrest\Type\IsObject;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use App\Jobs\GetOrderDetails;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use AWS\CRT\Auth\AwsCredentials;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Config\ConfigTrait;
use Illuminate\Support\Facades\Auth;
use SellingPartnerApi\Api\OrdersApi;
use SellingPartnerApi\Configuration;

use App\Services\SP_API\API\NewCatalog;
use Illuminate\Support\Facades\Artisan;
use Yajra\DataTables\Contracts\DataTable;
use function PHPUnit\Framework\returnSelf;
use App\Models\order\OrderSellerCredentials;
use App\Services\SP_API\Config\ConfigTrait as ConfigConfigTrait;

class OrdersListController extends Controller
{
    use ConfigTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::connection('order')->select('select amazon_order_identifier,purchase_date,last_update_date,order_status,order_total,number_of_items_shipped from orders');

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('order_total', function ($order_total) {
                    $price = '';
                    $price = json_decode($order_total->order_total);
                    if (isset($price->Amount) && isset($price->CurrencyCode)) {

                        $price = $price->Amount . ' [' . $price->CurrencyCode . ']';
                    }
                    return $price;
                })
                ->rawColumns(['order_total'])
                ->make(true);;
        }
        return view('orders.listorders.index');
    }

    public function GetOrdersList()
    {

        $source_key_exists = 0;
        $check_table = DB::connection('catalog')->select('SHOW TABLES');
        foreach ($check_table as $key => $table_name) {
            foreach ($table_name as $name_of_table) {
                if ($name_of_table == 'catalognewaes') {
                    $source_key_exists = 1;
                }
            }
        }
        if ($source_key_exists == 0) {
            $redbean = new NewCatalog();
            $redbean->RedBeanConnection();
            // $catalog_table = $catalog_table_name;
            $NewCatalogs = R::dispense('catalognewaes');
            $NewCatalogs->asin = '';
            R::store($NewCatalogs);
        }

        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            Log::warning("Export asin command executed local !");
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:sellers-orders-import > /dev/null &";
            exec($command);
        } else {

            Artisan::call('pms:sellers-orders-import ');
        }

        return redirect()->back();
    }

    public function OrderDetails(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::select('select amazon_order_identifier,purchase_date,last_update_date,order_status,fulfillment_channel,order_total,number_of_items_shipped,number_of_items_unshipped,shipping_address from orderdetails');
            // dd($data);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('order_total', function ($order_total) {
                    $price = '';
                    $price = json_decode($order_total->order_total);
                    if (isset($price->Amount) && isset($price->CurrencyCode)) {

                        $price = $price->Amount . ' [' . $price->CurrencyCode . ']';
                    }
                    return $price;
                })
                ->rawColumns(['order_total'])
                ->make(true);
        }

        return view('orders.ordersDetails.index');
    }

    public function GetOrderDetails()
    {
        // $token = 'Atzr|IwEBIG3zt3kKghE3Bl56OEGAxxeodmEzfaMAnMl0PivBlfumR8224Adu9lb33DKLEvHD6OBwdIBkaVlIZ5L2axypPm-LLuKPabvUCmRZ6F6C8KZKBJYS2u1sJVqzMxxoFSs6DTFLMxx8WBVXY395aKUzK3plz3-ttDN-YUGjiKR9-kFhLek1ZdjxwTQkvUdWdfpuDtcnW0veAPS0JUHVwTN39hpwJtPXm98XwD-wEe16n9qoWoak-UvtuML8irbdUdATSA4FLSX08H2V7SFAjdktXEW13v6gBs3xfCYn_w9Y4H29K5i5_vkQyiqj0j1FMK0nmtU';
        // $config = new Configuration([
        //     "lwaClientId" => config('app.aws_sp_api_client_id'),
        //     "lwaClientSecret" => config('app.aws_sp_api_client_secret'),
        //     "awsAccessKeyId" => config('app.aws_sp_api_access_key_id'),
        //     "awsSecretAccessKey" => config('app.aws_sp_api_access_secret_id'),
        //     "lwaRefreshToken" => $token,
        //     "roleArn" => config('app.aws_sp_api_role_arn'),
        //     "endpoint" => Endpoint::EU,
        // ]);
        // $host = config('database.connections.web.host');
        // $dbname = config('database.connections.web.database');
        // $port = config('database.connections.web.port');
        // $username = config('database.connections.web.username');
        // $password = config('database.connections.web.password');

        // R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

        $sellerOrders = DB::select('select seller_identifier,amazon_order_identifier from orders limit 10');
        foreach ($sellerOrders as $sellerOrder) {
            $seller_id =  $sellerOrder->seller_identifier;
            $order_id =  $sellerOrder->amazon_order_identifier;
            GetOrderDetails::dispatch(
                [
                    'order_id' => $order_id,
                    'seller_id' => $seller_id,
                ]
            );
        }
        // return redirect()->back();
    }

    public function OrderItemDetails(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::select('select order_identifier,asin,order_item_identifier,title,quantity_ordered,quantity_shipped,item_price from orderitems');
            // dd($data);
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('item_price', function ($order_total) {
                    $price = '';
                    $price = json_decode($order_total->item_price);
                    if (isset($price->Amount) && isset($price->CurrencyCode)) {

                        $price = $price->Amount . ' [' . $price->CurrencyCode . ']';
                    }
                    return $price;
                })
                ->rawColumns(['order_total'])
                ->make(true);
        }
        return view('orders.itemDetails.index');
    }

    public function GetOrderitems()
    {
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            Log::warning("Export asin command executed local !");
            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:seller-order-item-import > /dev/null &";
            exec($command);
        } else {

            Artisan::call('pms:seller-order-item-import ');
        }

        return redirect()->back();
    }
}
