<?php

namespace App\Services\Cliqnshop;

use Illuminate\Support\Str;

class SKU_Generator
{
    public function generateSKU($prefix, $ASIN)
    {
        $ASIN = (string) $ASIN;
        $new_asin = [];
        $first = $ASIN[0];
        $second = $ASIN[1];
        $third = $ASIN[2];
        for ($i = 0; $i < Str::length($ASIN); $i++) {
            if ($i == 2) {
                continue;
            }
            if ($i == 0) {
                array_push($new_asin, $prefix[0]);
            } else if ($i == 1) {
                array_push($new_asin, $prefix[1],$prefix[2]); //Adeed $prefix[2] here
            } else
            if ($i == 6) {
                array_push($new_asin, $first);
                array_push($new_asin, $ASIN[$i]);
            } else {
                array_push($new_asin, $ASIN[$i]);
            }
        }
        array_push($new_asin, $second);
        array_push($new_asin, $third);
        return implode('', $new_asin);
    }

    public function reverseSKU($prefix, $ASIN)
    {
        $ASIN = Str::substr($ASIN, Str::length($prefix));
        $new_asin = [];
        $len = Str::length($ASIN);
        for ($i = 0; $i < $len; $i++) {
            if ($i != 3) {
                array_push($new_asin, $ASIN[$i]);
            } else {
                $first = $ASIN[$i];
            }
        }
        $second = $new_asin[$len - 3];
        $third = $new_asin[$len - 2];
        unset($new_asin[$len - 3]);
        unset($new_asin[$len - 2]);
        $new_asin = implode('', $new_asin);
        return ($first . $second . $third . $new_asin);
    }
}
