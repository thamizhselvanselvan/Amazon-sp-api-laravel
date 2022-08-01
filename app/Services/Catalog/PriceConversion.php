<?php

namespace App\Services\Catalog;

class PriceConversion
{

    public function USAToIND($weight, $bb_price)
    {
        if ($weight > 0.9) {
            $int_shipping_base_charge = (6 + ($weight - 1) * 6);
        } else {
            $int_shipping_base_charge = 6;
        }

        $duty_rate = 32.00 / 100;
        $seller_commission = 10 / 100;
        $packaging = 2;
        $amazon_commission = 22.00 / 100;

        $ex_rate = 82;
        $duty_cost = round(($duty_rate * ($bb_price + $int_shipping_base_charge)), 2);

        $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
            (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $seller_commission);

        $usd_sp = round($price_befor_amazon_fees * (1 + $amazon_commission) +
            ($amazon_commission * $price_befor_amazon_fees * 0.12), 2);

        $india_sp = $usd_sp * $ex_rate;
        return $india_sp;
    }

    public function USATOUAE($weight, $bb_price)
    {
        $duty_rate = 5 / 100;
        $seller_commission = 10 / 100;
        $packaging = 4;
        $amazon_commission = 15.00 / 100;
        $int_shipping_base_charge = $weight * 4.5;
        $ex_rate = 3.7;
        $duty_cost = round(($duty_rate * ($bb_price + $int_shipping_base_charge)), 2);

        $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
            (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $seller_commission);

        $usd_sp = round($price_befor_amazon_fees * (1 + $amazon_commission) +
            ($amazon_commission * $price_befor_amazon_fees * 0.12), 2);

        $IED_sp = $usd_sp * $ex_rate;
        return round($IED_sp, 2);
    }

    public function USATOSG($weight, $bb_price)
    {
        if ($weight > 0.9) {
            $int_shipping_base_charge = (8 + ($weight - 1) * 4.5);
        } else {
            $int_shipping_base_charge = 8;
        }

        // return $int_shipping_base_charge;
        $duty_rate = 4.00 / 100;
        $seller_commission = 10 / 100;
        $packaging = 3;
        $MBM = 10.0 / 100;
        $amazon_commission = 12.00 / 100;

        $ex_rate = 1.37;
        $duty_cost = $duty_rate * $bb_price;

        $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
            (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $MBM);

        $mbm_usd_sp = $price_befor_amazon_fees * (1 + $amazon_commission) +
            ($amazon_commission * $price_befor_amazon_fees * 0.12);

        $sg_sp = $mbm_usd_sp * $ex_rate;

        return round($sg_sp, 2);
        //
    }
}
