<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ClouSale\AmazonSellingPartnerAPI\Configuration;

class TestController extends Controller
{

    public $europeToken = 'Atzr|IwEBIG6cb7Pd8or1Ot54HwC3q1fOLmNiKZJCqSRBwJ3mGsR6Qist7yNvnTVtFsMixWLR-ptJBXkXaTrhMMl9Yt3iXXCppyyovfXSInVX7fvkWz3L8KLreU3nrpa2LqaTGDpgsPHDD4UmbxJXV9kaS9HvC_pZ92VrecuyaF61UuzOciSTJymSPu6EPPErzcjWAySGJUU3LeXLfABKj89yKj8l_qewuKfPsn6_Dh59ZGOgOl52VtJ4A7IC8YDn1mGoMfJMyFkZIlZgaIkwvAsrSjwS61dwkXaGBRF4QqsY3OXrxn-nwU5kN9dnnnZaphRRvivBxr0';

	public $northToken = 'Atzr|IwEBICOoQYQS8c1k2IyuFJJY_xBzW1xnAqxFr1KFvcNj6sJeaZh5ZMIPxg2juchgyv4mAQFvfp5mgn4sTR0R-dyID4GP7VJzW7SqbKbcXlbmJCUus0PNpQubcRvjObFlLaMfPtqEcMnWHzSQOBDiU_FYt9VO5jozNikysAa5KoEHjCCM-Bl_I4f1uQ9AqBSQonwD9qy_kJSUUkaYjCjMu5jJNbZDkKCqTi34Lnx-lqifq33Xa9C03wnU0P4UwooySDFbPNjX0ml1Gm-nxAa3ldGzvzMAjzUrJti9QMxApvi9C7b7RzINWpiIF1daQSDXh_FttN0';

    public function getCatalogItem()
    {

    // require_once (__DIR__ . '/vendor/autoload.php');

    $options = [
        'refresh_token' => $this->europeToken, // Aztr|...
        'client_id' => 'amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf', // App ID from Seller Central, amzn1.sellerapps.app.cfbfac4a-......
        'client_secret' => '5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765', // The corresponding Client Secret
        'region' => \ClouSale\AmazonSellingPartnerAPI\SellingPartnerRegion::$EUROPE, // or NORTH_AMERICA / FAR_EAST
        'access_key' => 'AKIAZTIHMXYBD5SRG5IZ', // Access Key of AWS IAM User, for example AKIAABCDJKEHFJDS
        'secret_key' => '4DPad08/wrtdHHP2GFInzykOl6JWLzqhkEIeZ9UR', // Secret Key of AWS IAM User
        'endpoint' => \ClouSale\AmazonSellingPartnerAPI\SellingPartnerEndpoint::$EUROPE, // EUROPE or NORTH_AMERICA / FAR_EAST
        'role_arn' => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role', // AWS IAM Role ARN for example: arn:aws:iam::123456789:role/Your-Role-Name
    ];
    $accessToken = \ClouSale\AmazonSellingPartnerAPI\SellingPartnerOAuth::getAccessTokenFromRefreshToken(
        $options['refresh_token'],
        $options['client_id'],
        $options['client_secret']
    );
    $assumedRole = \ClouSale\AmazonSellingPartnerAPI\AssumeRole::assume(
        $options['region'],
        $options['access_key'],
        $options['secret_key'],
        $options['role_arn'],
    );
    $config = \ClouSale\AmazonSellingPartnerAPI\Configuration::getDefaultConfiguration();
    $config->setHost($options['endpoint']);
    $config->setAccessToken($accessToken);
    $config->setAccessKey($assumedRole->getAccessKeyId());
    $config->setSecretKey($assumedRole->getSecretAccessKey());
    $config->setRegion($options['region']);
    $config->setSecurityToken($assumedRole->getSessionToken());

    $apiInstance = new \ClouSale\AmazonSellingPartnerAPI\Api\CatalogApi($config);
    $marketplace_id = 'A21TJRUUN4KGV'; //india
    $asin = 'B08697KLZP'; //.in

    $result = $apiInstance->getCatalogItem($marketplace_id, $asin);
    echo $result->getPayload()->getAttributeSets()[0]->getTitle(); // Never Gonna Give You Up [Vinyl Single]

}

	
    public function getCompetitivePricing()
    {
        

    }

    public function getItemOffers()
    {

    }

    public function getPricing()
    {

    }

}
