<?php

use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Models\Aws_credential;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

if (!function_exists('ddp')) {
    function ddp($value)
    {
        echo "<pre>";
        var_dump($value);
        echo "</pre>";
    }
}

if (!function_exists('addPercentage')) {

    function addPercentage($originalAmount, $percentageChange)
    {
        return $originalAmount + ($percentageChange / 100) * $originalAmount;
    }
}

if (!function_exists('removePercentage')) {
    function removePercentage($originalAmount, $percentageChange)
    {
        return $originalAmount - ($percentageChange / 100) * $originalAmount;
    }
}

if (!function_exists('getPercentageChange')) {

    function getPercentageChange($oldAmount, $newAmount)
    {
        $decreaseValue = $oldAmount - $newAmount;

        return ($decreaseValue / $oldAmount) * 100;
    }
}

if (!function_exists('productDetailsDelete')) {

    function productDetailsDelete($table, $product_id)
    {

        $query = DB::table($table)->where('product_id', $product_id);
        $get_data = $query->get();

        if (!$get_data->isEmpty()) {
            $query->delete();
        }
    }
}

if (!function_exists('productDetailsDeleteWithAsin')) {

    function productDetailsDeleteWithAsin($table, $asin, $country_code)
    {

        $query = DB::table($table)->where('asin', $asin)->where('country_code', $country_code);
        $get_data = $query->get();

        if (!$get_data->isEmpty()) {
            $query->delete();
        }
    }
}

if (!function_exists('productsInsert')) {

    function productsInsert($table, $data)
    {

        DB::table($table)->insert($data);
    }
}

if (!function_exists('productOldDataDeleteAndInsertNewData')) {

    function productOldDataDeleteAndInsertNewData($table, $product_id, $data)
    {

        productDetailsDelete($table, $product_id);
        productsInsert($table, $data);
    }
}

if (!function_exists('productOldDataDeleteAndInsertNewDataASIN')) {

    function productOldDataDeleteAndInsertNewDataASIN($table, $asin, $country_code, $data)
    {

        productDetailsDeleteWithAsin($table, $asin, $country_code);
        productsInsert($table, $data);
    }
}

if (!function_exists('aws_credentials')) {
    function aws_credentials()
    {

        if (Auth::user()->roles->first()->name == "Seller") {

            return Aws_credential::with(['mws_region'])->where('seller_id', Auth::user()->id)->first();
        }
    }
}

if (!function_exists('slack_notification')) {
    function slack_notification($title, $message)
    {
        $webhook = config('pms.PMS_SLACK_NOTIFICATION_WEBHOOK');

        if (empty($webhook)) {
            throw new Exception("Please update your ENV with PMS_SLACK slack webhook url", 1);
        } else {
            //Notification::route('slack', $webhook)->notify(new SlackMessages($title, $message));
        }
    }
}

if (!function_exists('healthCheck')) {
    function healthCheck()
    {
        if (app()->environment('production')) {
            $generalHealthState = app('pragmarx.health')->checkResources();
            $msg = '';
            foreach ($generalHealthState as $obj) {

                if (!$obj->isHealthy()) {
                    $msg .= "{$obj->name}: {$obj->errorMessage}\n";
                }
            }
            if (!empty($msg)) {
                slack_notification("Health Check", $msg);
            }
        }
    }
}

if (!function_exists('is_developer')) {
    function is_developer()
    {
        return (in_array(Auth::user()->id, [1, 2])) ? true : false;
    }
}

if (!function_exists('is_admin')) {
    function is_admin()
    {

        if (Auth::user()->roles->first()->name == "Admin") {

            return true;
        }

        return false;
    }
}

if (!function_exists('startTime')) {
    function startTime()
    {
        return microtime(true);
    }
}

if (!function_exists('endTime')) {
    function endTime($start)
    {
        $time_elapsed_secs = microtime(true) - $start;
        print("Time elapsed: $time_elapsed_secs");

        return $time_elapsed_secs;
    }
}

if (!function_exists('showBreadcrumb')) {
    function showBreadcrumb()
    {
        return ucwords(str_replace([' ', '-', '_'], ['/', ' ', ' '], ucwords(str_replace(['/'], ' ', Route::current()->uri))));
    }
}

if (!function_exists('productBatch')) {
    function productBatch($products, $asin_limit, $request_no = 5)
    {
        $key = 0;
        $counter = 1;
        $request_counter = 0;
        $product_in_batch = [];

        foreach ($products as $product) {

            if ($counter <= $asin_limit) {

                $product_in_batch[$request_counter][$key][] = $product;
            }

            if ($counter == $asin_limit) {
                $counter = 0;
                $key++;
            }

            if ($request_no < count($product_in_batch[$request_counter])) {
                $request_counter++;
            }


            $counter++;
        }

        return $product_in_batch;
    }
}

if (!function_exists('productLowestPricedOffer')) {
    function productLowestPricedOffer($totalProducts = 200, $credentials = 1)
    {
        $delay = 18;
        $delay_seconds = $delay / $credentials;

        return  $delay_seconds * 1000;
    }
}

if(!function_exists('dateTimeFilter')) {
  function dateTimeFilter($data, $subDays = 6) {

      $period = new DatePeriod(Carbon::now()->subDays($subDays), CarbonInterval::day(), Carbon::today()->endOfDay());

      return array_reverse(array_map(function ($datePeriod) use ($data) {
          $date = $datePeriod->format('d-m-Y');
          return (isset($data[$date])) ? $data[$date] : 0;
      }, iterator_to_array($period)));
  }
}


if(!function_exists('dateTimeFiltermonthly')) {
    function dateTimeFiltermonthly($data, $subDays = 30) {
  
        $period = new DatePeriod(Carbon::now()->subDays($subDays), CarbonInterval::day(), Carbon::today()->endOfDay());
  
        return array_reverse(array_map(function ($datePeriod) use ($data) {
            $date = $datePeriod->format('d-m-Y');
            return (isset($data[$date])) ? $data[$date] : 0;
        }, iterator_to_array($period)));
    }
  }
  