<?php

namespace App\Services\SP_API\Config;

use Exception;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Configuration;

trait ConfigTrait
{

    public function  config($aws_key, $country_code, $auth_code = NULL)
    {
        $token = '';
        $region = $this->region_code($country_code);
        if ($auth_code) {

            $token = $auth_code;
        } else {

            $token = $this->token($aws_key);
        }
        $endpoints = ['EU' => Endpoint::EU, 'NA' => Endpoint::NA, 'FE' => Endpoint::FE];
Log::alert($token);
        return new Configuration([
            "lwaClientId" => config('app.aws_sp_api_client_id'),
            "lwaClientSecret" => config('app.aws_sp_api_client_secret'),
            "awsAccessKeyId" => config('app.aws_sp_api_access_key_id'),
            "awsSecretAccessKey" => config('app.aws_sp_api_access_secret_id'),
            "roleArn" => config('app.aws_sp_api_role_arn'),
            "lwaRefreshToken" => $token,
            "endpoint" => $endpoints[$region],
        ]);
    }

    public function token($aws_key)
    {
        $aws = Aws_credential::where('id', $aws_key)->first();

        if (!$aws) {
            return '';
        }

        return $aws->auth_code;
    }

    public function region_code($country_code)
    {

        $region_code = [
            "BR" => "NA",
            "CA" => "NA",
            "MX" => "NA",
            "US" => "NA",

            "AE" => "EU",
            "DE" => "EU",
            "EG" => "EU",
            "ES" => "EU",
            "FR" => "EU",
            "GB" => "EU",
            "IN" => "EU",
            "IT" => "EU",
            "NL" => "EU",
            "PL" => "EU",
            "SA" => "EU",
            "SE" => "EU",
            "TR" => "EU",

            "SG" => "FE",
            "AU" => "FE",
            "JP" => "FE",
        ];

        if (isset($region_code[$country_code])) {
            return $region_code[$country_code];
        }

        throw new Exception($country_code . " country code is Invalid. ");
    }

    public function marketplace_id($country_code)
    {

        $marketplace_id = [
            'BR' => 'A2Q3Y263D00KWC',
            'CA' => 'A2EUQ1WTGCTBG2',
            'MX' => 'A1AM78C64UM0Y8',
            'US' => 'ATVPDKIKX0DER',

            'AE' => 'A2VIGQ35RCS4UG',
            'DE' => 'A1PA6795UKMFR9',
            'EG' => 'ARBP9OOSHTCHU',
            'ES' => 'A1RKKUPIHCS9HS',
            'FR' => 'A13V1IB3VIYZZH',
            'GB' => 'A1F83G8C2ARO7P',
            'IN' => 'A21TJRUUN4KGV',
            'IT' => 'APJ6JRA9NG5V4',
            'NL' => 'A1805IZSGTT6HS',
            'PL' => 'A1C3SOZRARQ6R3',
            'SA' => 'A17E79C6D8DWNP',
            'SE' => 'A2NODRKZP88ZB9',
            'TR' => 'A33AVAJ2PDY3EV',

            'SG' => 'A19VAU5U5O7RUS',
            'AU' => 'A39IBJ37TRP1C6',
            'JP' => 'A1VC38T7YXB528',
        ];

        if (isset($marketplace_id[$country_code])) {
            return $marketplace_id[$country_code];
        }

        throw new Exception($country_code . " Countrycode is Inavlid for marketplace id.");
    }
}
