<?php

namespace App\Services\Catalog;

use App\Models\Admin\Ratemaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Catalog\ExchangeRate;

class PriceConversion
{
    private $rate_master_in_ae;
    private $rate_master_in_sa;
    private $rate_master_in_sg;
    private $exchange_rate_data;

    public function __construct()
    {
        $this->rate_master_in_ae = GetRateChart('IN-AE');

        $this->rate_master_in_sa = GetRateChart('IN-SA');

        $this->rate_master_in_sg = GetRateChart('IN-SG');

        $this->exchange_rate_data = ExchangeRate::select(
            'source_destination',
            DB::raw("group_concat(`base_weight`) as base_weight, 
                group_concat(`base_shipping_charge`) as base_shipping_charge,
                group_concat(packaging) as packaging,
                group_concat(seller_commission) as seller_commission,
                group_concat(duty_rate) as duty_rate,
                group_concat(sp_commission) as sp_commission,
                group_concat(excerise_rate) as excerise_rate,
                group_concat(amazon_commission) as amazon_commission
                ")
        )->groupBy('source_destination')->get()->toArray();
    }
    public function USAToINDB2C($weight, $bb_price)
    {
        if ($weight < 1) {
            $weight = 1;
        }

        $int_shipping_base_charge = ($this->exchange_rate_data[4]['base_weight'] + ($weight - 1) * $this->exchange_rate_data[4]['base_shipping_charge']);

        // if ($weight > 0.9) {
        // } else {
        //     $int_shipping_base_charge = $this->exchange_rate_data[4]['base_shipping_charge'];
        // }

        $duty_rate = $this->exchange_rate_data[4]['duty_rate'] / 100;
        $packaging = $this->exchange_rate_data[4]['packaging'];
        $seller_commission = ($bb_price + $int_shipping_base_charge + $packaging) * (($this->exchange_rate_data[4]['seller_commission']) / 100);

        $price_before_duty = $bb_price + $int_shipping_base_charge + $packaging + $seller_commission;
        $ex_rate = $this->exchange_rate_data[4]['excerise_rate'];
        $duty_cost = ($price_before_duty * $duty_rate);

        $usd_sp = ($price_before_duty + $duty_cost) + ($price_before_duty + $duty_cost) * (($this->exchange_rate_data[4]['sp_commission']) / 100);

        $india_sp = $usd_sp * $ex_rate;
        return round($india_sp, 2);
    }

    public function USAToINDB2B($weight, $bb_price)
    {
        if ($weight < 1) {
            $weight = 1;
        }

        $int_shipping_base_charge = ($this->exchange_rate_data[3]['base_weight'] + ($weight - 1) * $this->exchange_rate_data[3]['base_shipping_charge']);

        // if ($weight > 0.9) {
        //     $int_shipping_base_charge = ($this->exchange_rate_data[3]['base_weight'] + ($weight - 1) * $this->exchange_rate_data[3]['base_shipping_charge']);
        // } else {
        //     $int_shipping_base_charge = $this->exchange_rate_data[3]['base_shipping_charge'];
        // }

        $duty_rate = $this->exchange_rate_data[3]['duty_rate'] / 100;
        $seller_commission = $this->exchange_rate_data[3]['seller_commission'] / 100;
        $packaging = $this->exchange_rate_data[3]['packaging'];
        $amazon_commission = $this->exchange_rate_data[3]['amazon_commission'] / 100;

        $ex_rate = $this->exchange_rate_data[3]['excerise_rate'];
        $duty_cost = ($duty_rate * ($bb_price + $int_shipping_base_charge));

        $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
            (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $seller_commission);

        $usd_sp = $price_befor_amazon_fees * (1 + $amazon_commission) +
            ($amazon_commission * $price_befor_amazon_fees * $this->exchange_rate_data[3]['sp_commission']);

        $india_sp = $usd_sp * $ex_rate;
        return round($india_sp, 2);
    }

    public function USATOUAE($weight, $bb_price)
    {
        $weight = (float)$weight;
        $bb_price = (float)$bb_price;

        if ($weight < 0.5) {

            $weight = 0.5;
        }

        $duty_rate = $this->exchange_rate_data[6]['duty_rate'] / 100;
        $seller_commission = $this->exchange_rate_data[6]['seller_commission'] / 100;
        $packaging = $this->exchange_rate_data[6]['packaging'];
        $amazon_commission = $this->exchange_rate_data[6]['amazon_commission'] / 100;
        $int_shipping_base_charge = $weight * $this->exchange_rate_data[6]['base_shipping_charge'];
        $ex_rate = $this->exchange_rate_data[6]['excerise_rate'];
        $duty_cost = ($duty_rate * ($bb_price + $int_shipping_base_charge));

        $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
            (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $seller_commission);

        $usd_sp = $price_befor_amazon_fees * (1 + $amazon_commission) +
            ($amazon_commission * $price_befor_amazon_fees * $this->exchange_rate_data[6]['sp_commission']);

        $IED_sp = $usd_sp * $ex_rate;
        return round($IED_sp, 2);
    }

    public function USATOSG($weight, $bb_price)
    {
        $weight = (float)$weight;
        $bb_price = (float)$bb_price;

        if ($weight > 0.9) {

            $int_shipping_base_charge = ($this->exchange_rate_data[5]['base_weight'] + ($weight - 1) * $this->exchange_rate_data[5]['base_shipping_charge']);
        } else {
            $int_shipping_base_charge = $this->exchange_rate_data[5]['base_weight'];
        }

        // return $int_shipping_base_charge;
        $duty_rate = $this->exchange_rate_data[5]['duty_rate'] / 100;
        // $seller_commission = 10 / 100;
        $packaging = $this->exchange_rate_data[5]['packaging'];
        $MBM = $this->exchange_rate_data[5]['seller_commission'] / 100;
        $amazon_commission = $this->exchange_rate_data[5]['amazon_commission'] / 100;

        $ex_rate = $this->exchange_rate_data[5]['excerise_rate'];
        $duty_cost = $duty_rate * $bb_price;

        $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
            (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $MBM);

        $mbm_usd_sp = $price_befor_amazon_fees * (1 + $amazon_commission) +
            ($amazon_commission * $price_befor_amazon_fees * $this->exchange_rate_data[5]['sp_commission']);

        $sg_sp = $mbm_usd_sp * $ex_rate;

        return round($sg_sp, 2);
        //
    }

    public function INDToSA($weight, $bb_price)
    {
        $weight = ceil((float)$weight);
        $bb_price = (float)$bb_price;

        $rate_array  = $this->rate_master_in_sa;
        $int_shipping_base_charge = 0;
        foreach ($rate_array as $key => $value) {

            $key = (float)$key;
            if ($key >= $weight) {
                $int_shipping_base_charge = $value['lmd_cost'];
                break;
            }
        }

        $duty_rate = $this->exchange_rate_data[0]['duty_rate'] / 100;
        $nitshopp = $this->exchange_rate_data[0]['seller_commission'] / 100;
        $packaging = $this->exchange_rate_data[0]['packaging'];
        $amazon_commission = $this->exchange_rate_data[0]['amazon_commission'] / 100;
        $ex_rate = $this->exchange_rate_data[0]['excerise_rate'];

        $duty_cost = ($bb_price + $int_shipping_base_charge) * $duty_rate;

        $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
            (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $nitshopp);

        $mbm_usd_sp = $price_befor_amazon_fees * (1 + $amazon_commission) +
            ($amazon_commission * $price_befor_amazon_fees * $this->exchange_rate_data[0]['sp_commission']);

        $uae_sa = $mbm_usd_sp * $ex_rate;

        return round($uae_sa, 2);
    }

    public function INDToSG($weight, $bb_price)
    {
        $weight = ceil((float)$weight);
        $bb_price = (float)$bb_price;

        $rate_array  = $this->rate_master_in_sg;
        //India to Singapore
        $int_shipping_base_charge = 0;
        foreach ($rate_array as $key => $value) {
            $key = (float)$key;
            if ($key >= $weight) {
                $int_shipping_base_charge = $value['lmd_cost'];
                break;
            }
        }

        $duty_rate = $this->exchange_rate_data[1]['duty_rate'] / 100;
        $nitshopp = $this->exchange_rate_data[1]['seller_commission'] / 100;
        $packaging = $this->exchange_rate_data[1]['packaging'];
        $amazon_commission = $this->exchange_rate_data[1]['amazon_commission'] / 100;
        $ex_rate = $this->exchange_rate_data[1]['excerise_rate'];

        $duty_cost = ($bb_price + $int_shipping_base_charge) * $duty_rate;

        $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
            (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $nitshopp);

        $mbm_usd_sp = $price_befor_amazon_fees * (1 + $amazon_commission) +
            ($amazon_commission * $price_befor_amazon_fees * $this->exchange_rate_data[1]['sp_commission']);

        $uae_sg = $mbm_usd_sp * $ex_rate;

        return round($uae_sg, 2);
    }

    public function INDToUAE($weight, $bb_price)
    {
        $weight = ceil((float)$weight);
        $bb_price = (float)$bb_price;

        $rate_array = $this->rate_master_in_ae;
        $int_shipping_base_charge = 0;

        foreach ($rate_array as $key => $value) {

            $key = (float)$key;
            if ($key >= $weight) {
                $int_shipping_base_charge = $value['lmd_cost'];
                break;
            }
        }
        $duty_rate = $this->exchange_rate_data[2]['duty_rate'] / 100;
        $nitshopp = $this->exchange_rate_data[2]['seller_commission'] / 100;
        $packaging = $this->exchange_rate_data[2]['packaging'];
        $amazon_commission = $this->exchange_rate_data[2]['amazon_commission'] / 100;
        $ex_rate = $this->exchange_rate_data[2]['excerise_rate'];

        $duty_cost = ($bb_price + $int_shipping_base_charge) * $duty_rate;

        $price_befor_amazon_fees = ($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) +
            (($bb_price + $int_shipping_base_charge + $duty_cost + $packaging) * $nitshopp);

        $mbm_usd_sp = $price_befor_amazon_fees * (1 + $amazon_commission) +
            ($amazon_commission * $price_befor_amazon_fees * $this->exchange_rate_data[2]['sp_commission']);

        $uae_sp = $mbm_usd_sp * $ex_rate;

        return round($uae_sp, 2);
    }
}
