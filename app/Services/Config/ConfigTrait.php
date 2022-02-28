<?php

namespace App\Services\Config;

use Exception;
use App\Models\Aws_credentials;
use SellingPartnerApi\Endpoint;
use SellingPartnerApi\Configuration;
use Illuminate\Support\Facades\Log;

trait ConfigTrait
{

    public function config($aws_key, $country_code, $auth_code)
    {
       
        $token = '';
        $region = $this->region_code($country_code);
        //$region = 'NAzzz';
        // $token = $this->token($aws_key);

        $refeshtoken = 'Atzr|IwEBIO2ZVAsEZ1E-gDCJb5IUPCZJ5D4VlBLuHj_84aR7zWgflDotXdNyNoX-34zERwG7si1VlwP4Y-wBFVxG8lZT5gkG1y8fgDDTmrNJh0LLcagPJeOMPmckwk5RWCcUCUU-0ifPyutYk-X9RLAsDEZc4lZ6JeKcKphQ_T7Vy0sXRtR_fBhGdbkS2TSqpTqELrWa8DSDuRMAQEzVPgaVrFgXRYMEkeGysre0R_iVz7r5lb7w0Yhcx9VDW7tMGpAJe1P-5bcsbE6wmRIzsa8eCh_HTVnc_4LTsEvAEmJRsIarURRayppQul2azLbwep-4eVv_9r0';
        $endpoints = ['EU' => Endpoint::EU, 'NA' => Endpoint::NA, 'FE' => Endpoint::FE];
        $clientId = 'amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf';
        $clientSecret = '5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765';
        $awsAccessKeyId = 'AKIAZTIHMXYBD5SRG5IZ';
        $awsSecretAccessKey = '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';
        $roleArn = 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role';

        $refeshtoken = 'Atzr|IwEBIJRFy0Xkal83r_y4S7sGsIafj2TGvwfQc_rppZlk9UzT6EuqEn9SaHmQfNbmEhOtk8Z6Dynk43x15TpyS3c2GuybzctGToAmjwGxiWXCwo2M3eQvOWfVdicOaF1wkivMAVH8lO8Qt3LtvCNjk5yiRsY5zPTJpShWRqiZ570lpcVb8D1HghZRQCaluoGkuVNOKZquXBF4KSwLur6duoDrUw5ybAIECAMclRbNtUulG9X2T902Wg6dKBSKq_3R-cNbOQ2Ld3-iSguanUI5SsSJOjdVJRpzuTkcWL2GcdFCSlp6NHnRV-2NLCcvZi3ZLtkonIg';

        return  new Configuration([
            "lwaClientId" => $clientId,
            "lwaClientSecret" => $clientSecret,
            "awsAccessKeyId" => $awsAccessKeyId,
            "awsSecretAccessKey" => $awsSecretAccessKey,
            "roleArn" => $roleArn,
            "lwaRefreshToken" => $refeshtoken,
            "endpoint" => $endpoints[$region],
        ]);
    }

    public function token($aws_key)
    {
        $aws = Aws_credentials::where('id', $aws_key)->first();

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
