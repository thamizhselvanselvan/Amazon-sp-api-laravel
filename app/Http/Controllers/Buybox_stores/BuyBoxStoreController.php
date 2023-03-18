<?php

namespace App\Http\Controllers\Buybox_stores;

use in;
use Illuminate\Http\Request;
use App\Models\Aws_credential;
use Illuminate\Support\Facades\DB;
use function Clue\StreamFilter\fun;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Buybox_stores\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Buybox_stores\Product_Push;
use App\Services\SP_API\Config\ConfigTrait;
use App\Models\Buybox_stores\Seller_id_name;
use App\Models\order\OrderSellerCredentials;
use App\Models\Buybox_stores\Product_push_ae;
use App\Models\Buybox_stores\Product_Push_in;
use App\Services\AmazonFeedApiServices\AmazonFeedProcess;
use App\Models\Buybox_stores\Product_availability_ae;
use App\Models\Buybox_stores\Product_availability_in;

use JeroenNoten\LaravelAdminLte\View\Components\Form\Input;
use App\Services\AmazonFeedApiServices\AmazonFeedProcessAvailability;

class BuyBoxStoreController extends Controller
{
    use ConfigTrait;

    public function index()
    {
        $stores = ['7', '8', '9', '10', '12', '20', '27'];

        $stores = Aws_credential::query()
            ->whereIN('seller_id', $stores)
            ->select('seller_id', 'store_name')
            ->get();

        return view('buybox_stores.index', compact('stores'));
    }

    public function latencyupdate(Request $request)
    {
        $asin = $request->asin;
        $store_id = $request->store;
        $latency = $request->latency;

        if ($latency == null || $asin == null || $store_id == null) {
            return redirect()->route('buybox.stores')->with('error', 'Please Add All the 3 Fields');
        }

        $request->validate([
            'asin' => 'required',
            'store' => 'required',
            'latency' => 'required',
        ]);
        Product::query()
            ->where(['asin' => $asin, 'store_id' => $store_id])
            ->update(['latency' => $latency]);

        return redirect()->route('buybox.stores')->with('success', 'Latency has Updated successfully');
    }

    public function exportall(Request $request)
    {
        commandExecFunc("mosh:export_all_stores_asins");
        return redirect()->route('buybox.stores')->with('success', 'Export Started Please Wait For Some time...');
    }

    public function exportdownload(Request $request)
    {
        $catalogfiles = [];
        $folder = $request->catalog;
        $path = (Storage::path($folder));
        $files = scandir($path);

        foreach ($files as $key => $file) {
            if ($key > 1) {
                $catalogfiles[$file] = date("F d Y H:i:s.", filemtime($path . '/' . $file));
            }
        }
        return response()->json($catalogfiles);
    }

    public function DownloadCataloglocal($index)
    {
        return Storage::download('aws-products/exports/' . $index);
    }

    public function get_price_push(Request $request)
    {

        if ($request->ajax()) {

            $results = Product_Push::query()
                ->select('id', 'store_id', 'product_sku', 'latency', 'push_price', 'base_price')
                ->orderBy('id', 'DESC')
                ->get();

            return DataTables::of($results)
                // ->addColumn('status', function ($file_management) {
                //     $process = $file_management['command_end_time'] == '0000-00-00 00:00:00' ? 'Processing...' : 'Processed';
                //     return $process;
                // })
                // ->rawColumns(['id', 'user_name', 'type', 'module', 'start_time', 'end_time', 'processed_time', 'status'])
                ->make(true);
        }

        return view('buybox_stores.sp_api_push');
    }

    public function storeslisting(Request $request)
    {

        $stores = OrderSellerCredentials::select('store_name', 'seller_id')
            ->where('buybox_stores', 1)
            ->whereIn("seller_id", [6, 7, 8, 9, 10, 27, 29, 35, 44])
            ->distinct()
            ->get();

        $request_store_id = $request->store_id;
        $url = "/stores/listing/price";

        if (isset($request_store_id)) {
            $url = "/stores/listing/price/" . $request_store_id;
        }

        $files = Storage::files('public/product_push');
        $new_files = [];

        foreach ($files as $file) {
            $new_files[] = '/storage/product_push/' . basename($file);
        }

        if ($request->ajax()) {

            $select_query = [
                'id',
                'store_id',
                'asin',
                'product_sku',
                'push_price',
                'current_store_price',
                'bb_winner_price',
                'bb_winner_id',
                'base_price',
                'ceil_price',
                'app_360_price',
                'highest_seller_price',
                'highest_seller_id',
                'lowest_seller_price',
                'lowest_seller_id',
                'applied_rules'
            ];

            if (in_array($request_store_id, [13, 14, 5, 6, 8, 16, 17, 10, 21, 23, 24, 26, 27, 28, 30, 31, 33, 36, 39])) {

                $data = Product_Push_in::query()
                    ->select($select_query)
                    ->where('store_id', $request_store_id)
                    ->where('push_status', 0)
                    ->where('availability', 1)
                    ->orderBy('id', 'DESC');
            } else {

                $data = Product_push_ae::query()
                    ->select($select_query)
                    ->where('store_id', $request_store_id)
                    ->where('push_status', 0)
                    ->where('availability', 1)
                    ->orderBy('id', 'DESC');
            }

            return DataTables::of($data)
                ->editColumn('highest_seller_name', function ($query) use ($request_store_id) {

                    //$seller_name = (Seller_id_name::where('seller_store_id', $query->highest_seller_id)->first())->seller_name ?? "";
                    $seller_name = Cache::get($query->highest_seller_id, function () use ($query) {
                        return Seller_id_name::where('seller_store_id', $query->highest_seller_id)->first();
                    });

                    if (isset($seller_name->seller_name) && strlen($seller_name->seller_name) > 0) {
                        return $seller_name->seller_name;
                    }

                    if (isset($query->bb_winner_id) && strlen($query->bb_winner_id) > 0) {

                        return $this->amazon_links($request_store_id, $query->bb_winner_id);
                    }

                    return "None";

                    // $seller_name = $seller_name->seller_name ?? "";

                    // $highest_seller = (isset($seller_name)) ? $seller_name : $query->highest_seller_id ;

                    // return (isset($highest_seller) && $highest_seller != "") ? $highest_seller : "None" ;
                })
                ->editColumn('lowest_seller_name', function ($query) use ($request_store_id) {

                    $seller_name = Cache::get($query->lowest_seller_id, function () use ($query) {
                        return Seller_id_name::where('seller_store_id', $query->lowest_seller_id)->first();
                    });

                    if (isset($seller_name->seller_name) && strlen($seller_name->seller_name) > 0) {
                        return $seller_name->seller_name;
                    }

                    if (isset($query->bb_winner_id) && strlen($query->bb_winner_id) > 0) {

                        return $this->amazon_links($request_store_id, $query->bb_winner_id);
                    }

                    return "None";

                    // $seller_name = $seller_name->seller_name ?? "";

                    // $lowest_seller = (isset($seller_name)) ? $seller_name : $query->lowest_seller_id ;

                    // return (isset($lowest_seller) && $lowest_seller != "") ? $lowest_seller : "None" ;
                })
                ->editColumn('bb_winner_id', function ($query) use ($request_store_id) {

                    $seller_name = Cache::get($query->bb_winner_id, function () use ($query) {
                        return Seller_id_name::where('seller_store_id', $query->bb_winner_id)->first();
                    });

                    if (isset($seller_name->seller_name) && strlen($seller_name->seller_name) > 0) {
                        return $seller_name->seller_name;
                    }

                    if (isset($query->bb_winner_id) && strlen($query->bb_winner_id) > 0) {

                        return $this->amazon_links($request_store_id, $query->bb_winner_id);
                    }
                    return "None";

                    //$seller_name = $seller_name->seller_name ?? "";

                    // Log::debug("$seller_name ===== $query->bb_winner_id");

                    //$bb_winner = (isset($seller_name) && strlen($seller_name) > 0) ? $seller_name : $query->bb_winner_id;

                    //  Log::notice((isset($bb_winner) && $bb_winner != "") ? $bb_winner : "None");

                    //  return (isset($bb_winner) && $bb_winner != "") ? $this->amazon_links($bb_winner) : "None" ;
                })
                ->editColumn('asin', function ($query) {

                    return "<a target='_blank' href='https://amazon.com/dp/" . $query->asin . "'>" . $query->asin . "</a>";
                })
                ->editColumn('product_sku', function ($query) {

                    $seller_id = $query->store_id;
                    $data = Aws_credential::with(['mws_region'])->where('seller_id', $seller_id)->get()->toArray();
                    $region_code = strtolower($data[0]['mws_region']['region_code']);

                    return "<a target='_blank' href='https://amazon." . $region_code . "/dp/" . $query->asin . "'>" . $query->product_sku . "</a>";
                })
                ->editColumn('current_store_price', function ($query) {

                    $applied_rules = '<div class="pop_over position-relative"> ' . $query->current_store_price . ' ' . $this->pop_over_data($query->applied_rules) . '</div>';

                    return $applied_rules;
                })
                ->addColumn('action', function ($query) {

                    return "<button class='price_process btn btn-sm btn-primary'
                              asin='{$query->asin}' productsku=='{$query->product_sku}' pushprice='{$query->push_price}' storeid={$query->store_id} data-id={$query->id} 
                              base_price={$query->base_price} ceil_price={$query->ceil_price}
                            >Process</button>";
                })
                ->rawColumns(['action', 'asin', 'product_sku', 'highest_seller_name', 'lowest_seller_name', 'bb_winner_id', 'current_store_price'])
                ->make(true);
        }

        return view('buybox_stores.listing', compact('stores', 'url', 'request_store_id', 'new_files'));
    }

    public function pop_over_data($applied_rules)
    {
        $html = '<span class="d-block"> No Rules Applied </span>';
        if ($applied_rules) {

            $applied_rules = json_decode($applied_rules, true);

            if (count($applied_rules) > 0) {

                $html = '<ul class="m-0 p-0 pl-3">';
                foreach ($applied_rules as $applied_rule) {

                    $html .= '<li class="mt-1">' . $applied_rule . '</li>';
                }

                $html .= '</ul>';
            }
        }
        //comment

        return '<div class="pop_over_data position-absolute shadow border d-none">' . $html . '</div>';
    }


    public function availability(Request $request)
    {
        $stores = OrderSellerCredentials::select('store_name', 'seller_id')
            ->where('buybox_stores', 1)
            ->where('seller_id', 6)
            ->distinct()
            ->get();

        $request_store_id = $request->store_id;
        $url = "/stores/listing/availability";

        if (isset($request_store_id)) {
            $url = "/stores/listing/availability/" . $request_store_id;
        }

        if ($request->ajax()) {

            if (in_array($request_store_id, [13, 14, 5, 6, 8, 16, 17, 10, 21, 23, 24, 26, 27, 28, 30, 31, 33, 36, 39])) {

                $data = Product_availability_in::query();
            } else {

                $data = Product_availability_ae::query();
            }

            $data->select('id', 'store_id', 'asin', 'product_sku', 'push_availability', 'current_availability')
            ->where('store_id', $request_store_id)
            ->where('push_status', 0)
            ->orderBy('id', 'DESC');

            return DataTables::of($data)
                ->editColumn('asin', function ($query) {
                    return "<a target='_blank' href='https://amazon.com/dp/" . $query->asin . "'>" . $query->asin . "</a>";
                })
                ->editColumn('product_sku', function ($query) {

                    $seller_id = $query->store_id;
                    $data = Aws_credential::with(['mws_region'])->where('seller_id', $seller_id)->get()->toArray();
                    $region_code = strtolower($data[0]['mws_region']['region_code']);

                    return "<a target='_blank' href='https://amazon." . $region_code . "/dp/" . $query->asin . "'>" . $query->product_sku . "</a>";
                })
                // ->addColumn('availability', function ($query) {

                //     if ($query->availability == '1') {

                //         $action = "<input type='checkbox' id='price_availability' name='availability' value='$query->availability' data-id='$query->id' checked />";
                //     } else {
                //         $action = "<input type='checkbox' id='price_availability' name='availability' value='$query->availability' data-id='$query->id' />";
                //     }
                //     return $action;
                // })

                ->addColumn('action', function ($query) {

                    $action = "<a href='javascript:void(0)' id='update_availability' class='edit btn btn-info btn-sm ml-2' "; 
                    $action .= " data-seller_id='$query->store_id' data-product_id='$query->id' data-product_sku='$query->product_sku' data-asin='$query->asin' data-current_availability='$query->current_availability' data-availability='$query->push_availability' >
                    Update Availability
                </a>";
                    return $action;
                })
                ->rawColumns(['asin', 'product_sku', 'availability', 'action'])
                ->make(true);
        }

        return view('buybox_stores.availability', compact('stores', 'url', 'request_store_id'));
    }

    public function PricePushAvailability(Request $request)
    {

        $seller_id = $request->seller_id;
        $regionCode = strtolower($request->region);
        $product_push_id = $request->id;
        $availability = $request->availability;

        $feedLists[] = [
            'product_sku' => $request->product_sku,
            'available' => $availability
        ];

        // if ($regionCode == 'in') {

        //     Product_Push_in::query()->where('id', $product_push_id)->update(['availability' => $availability]);
        // } elseif ($regionCode == 'ae') {

        //     Product_Push_ae::query()->where('id', $product_push_id)->update(['availability' => $availability]);
        // }

        $PushAvailability = new AmazonFeedProcessAvailability();
        $data = $PushAvailability->availabilitySubmit($feedLists, $seller_id, $product_push_id, $regionCode);
            
        return response()->json(['data' => $data]);
    }

    public function amazon_links($store_id, $merchant_id)
    {

        if (in_array($store_id, [13, 14, 5, 6, 8, 16, 17, 10, 21, 23, 24, 26, 27, 28, 30, 31, 33, 36, 39])) {
            return "<a href='https://amazon.in/sp?seller=" . $merchant_id . "'>$merchant_id</a>";
        }

        if (in_array($store_id, [15, 7, 18, 9, 29, 32, 35, 38, 44])) {
            return "<a href='https://amazon.ae/sp?seller=" . $merchant_id . "'>$merchant_id</a>";
        }

        return $merchant_id;
    }

    public function push_price_update(Request $request)
    {

        $id = $request->id;
        $product_sku = $request->productsku;
        $push_price = $request->pushprice;
        $store_id = $request->storeid;
        $base_price = $request->base_price;
        $ceil_price = $request->ceil_price;

        $feedLists[] = [
            "push_price" => $push_price,
            "product_sku" => $product_sku,
            "base_price" => $base_price,
            "ceil_price" => $ceil_price,
        ];

        //jobDispatchFunc("Amazon_Feed\AmazonFeedPriceAvailabilityPush", $price_update);
        $price_update = (new AmazonFeedProcess)->feedSubmit($feedLists, $store_id, $id);

        if ($price_update) {
            return ["success" => true];
        }

        return ["failed" => true];
    }

    public function updateprice(Request $request)
    {
        //command to execute
        // commandExecFunc('');

        echo $request->id;
    }

    public function updatepricelisting(Request $request)
    {
        // $data =  Product_Push::query()
        //     ->where('push_status', '1')
        //     ->get();

        // if ($request->ajax()) {
        //     return DataTables::of($data)
        //         ->addIndexColumn()
        //         ->make(true);
        // }
        return view('buybox_stores.update_listing');
    }

    public function store_data_export(Request $request)
    {


        if (!$request->has("store_id")) {
            return "error";
        }

        $store_id = $request->store_id;

        commandExecFunc("mosh:bb:product_push:export $store_id");
        return 'success';
    }

    public function list_all_the_export()
    {

        // Get all CSV files in the directory
        $files = Storage::files('product_push');

        if (count($files) > 0) {
            return response()->json(['error' => "No Files are there to show"]);
        }

        // Set the file retention period to 30 days
        $fileRetentionPeriod = 3;

        // Iterate through each file and delete older files
        foreach ($files as $file) {
            $fileModifiedDate = Storage::lastModified($file);
            $dateDifference = date_diff(date_create(), date_create("@$fileModifiedDate"))->format("%a");

            if ($dateDifference > $fileRetentionPeriod) {
                Storage::delete($file);
            }
        }

        return response()->json($files);
    }

    public function fileget(Request $request)
    {
        if ($request->ajax()) {

            $storefiles = [];
            $folder = $request->path;
            $path = (Storage::path($folder));
            $files = scandir($path);

            foreach ($files as $key => $file) {
                if ($key > 1) {
                    $storefiles[$file] = date("F d Y H:i:s.", filemtime($path . '/' . $file));
                }
            }
            return response()->json($storefiles);
        }
    }

    public function filedownload($index)
    {
        return Storage::download('public/product_push/' . $index);
    }


    public function requestregion(Request $request)
    {
        if ($request->ajax()) {
            $country_code = $request->region;

            $data =   OrderSellerCredentials::query()
                ->where(['country_code' => $country_code, 'buybox_stores' => 1])
                ->select('store_name', 'seller_id')
                ->get();

            return response()->json($data);
        }
    }
}
