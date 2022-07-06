<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\View\View;
use App\Models\System;
use App\Models\FirebaseSync;
use App\Reports\Sample;
use Illuminate\Support\Facades\Storage;
use DB;
use Auth;
use File;
use App\Models\Auth\UserMeta;
use App\Models\Auth\User;
use App\Models\Affiliate\AffiliateTree;
use App\Models\Subject\Subjects;
use App\Models\Quiz\Quiz;
use Kreait\Firebase;
use App\Jobs\MadmimiFormJob;
use Carbon\Carbon;

class RobinTest extends Controller
{

    public function showPHPInfo()
    {
        phpinfo();
    }

    public function getDirContents(String $dir, &$results = array()): ?array
    {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                self::getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }

    public function listUrls()
    {
        $routeCollection = Route::getRoutes();
        $lists = array();
        foreach ($routeCollection as $value) {
            $key = $value->uri();
            if (true || !preg_match('/_debugbar|api|oauth|ignition|tinker|health|ggc|horizon|debugger|impersonate|password|lang/i', $key)) {

                $explode = explode('/', $value->uri());

                if (isset($explode[1])) {
                    $lists[$value->methods[0]][$explode[0]][] = $value->uri();
                } else {
                }
            }
        }

        ksort($lists);
        return view('robin/url-lists', ['data' => $lists]);
    }

    public function pmsConfigCacheTest()
    {
        echo "Key: pms.GS_PREFIX <br>Value:" . config('pms.GS_PREFIX') . "<HR>";
        echo "Key: pms.PMS_PREFIX <br>Value:" . config('pms.PMS_PREFIX') . "<HR>";

        exit;
    }

    public function systemConfigCacheTest()
    {

        echo config('pms.PMS');

        msg("app.env: " . config('app.env'));
        echo "<HR>";

        echo ("Key: pms.LARAVEL_IONIC_SALT <br>Value: " . config('pms.LARAVEL_IONIC_SALT') . "<hr>");
        echo ("Key: pms.APP_DEEPLINK_LOGIN_URL <br>Value: " . config('pms.APP_DEEPLINK_LOGIN_URL') . "<hr>");

        exit;
    }

    public function showEnv()
    {
        po($_ENV);
    }

    public function showServer()
    {
        po($_SERVER);
    }

    public function memTest()
    {

        $key = rand(1, 10);
        msg("Key is $key");
        echo "<HR>";
        msg("Getting cache time");
        $value = Cache::get($key);
        msg("Here is the cache $value");
        echo "<HR>";

        $time = date('Y-m-d H:i:s');
        msg("Setting cache $time");
        Cache::put($key, $time, 600);

        echo $value;
        echo "<HR>";

        $value1 = Cache::get('1');
        $value2 = Cache::get('2');
        $value3 = Cache::get('3');
        $value4 = Cache::get('4');
        $value5 = Cache::get('5');
        $value6 = Cache::get('6');
        $value7 = Cache::get('7');
        $value8 = Cache::get('8');
        $value9 = Cache::get('9');

        po("1) " . $value1);
        po("2) " . $value2);
        po("3) " . $value3);
        po("4) " . $value4);
        po("5) " . $value5);
        po("6) " . $value6);
        po("7) " . $value7);
        po("8) " . $value8);
        po("9) " . $value9);
    }

    public function redisTest()
    {
        msg("Default");
        $keys = Redis::connection('default')->keys('*');
        foreach ($keys as $key => $k) {
            po($k);
        }
        echo "<HR>";

        msg("Model Cache");
        $keys = Redis::connection('cache')->keys('*');
        foreach ($keys as $key => $k) {
            if (preg_match('/model/i', $k)) {
                po($k);
            }
        }
        echo "<HR>";

        msg("Cache");
        $keys = Redis::connection('cache')->keys('*');
        foreach ($keys as $key => $k) {
            if (!preg_match('/model/i', $k)) {
                po($k);
            }
        }
        echo "<HR>";

        msg("Queue");
        $keys = Redis::connection('queue')->keys('*');
        foreach ($keys as $key => $k) {
            po($k);
        }
        echo "<HR>";

        msg("Session");
        $keys = Redis::connection('session')->keys('*');
        foreach ($keys as $key => $k) {
            po($k);
        }
        echo "<HR>";

        msg("Base Nodes");
        $keys = Redis::connection('horizon')->keys('*');
        foreach ($keys as $key => $k) {
            po($k);
        }
    }

    public function showConfig()
    {
        msg('showConfig');

        config("robin", "singh");

        msg(config('CACHE_DRIVER'));

        $key = 'key3';

        msg("Key value is $key");

        if (Cache::has($key)) {
            msg("Cache value found for key");
            msg(Cache::get($key));
        } else {
            msg("Key is not in cache");
            $time = date('Y-m-d H:i:s');
            Cache::put($key, $time);
        }
    }

    public function databaseCache()
    {
        $systemData = System::all();

        $seconds = 200;

        $value = Cache::remember('robin', $seconds, function () {
            return 'singh';
        });

        po($value);
    }

    public function showServerTime()
    {
        echo date("F j, Y, g:i a");
    }


}
