<?php

namespace App\Services;

use Exception;

class getcompetitivePricing
{

	public $options = [];
	public $config = [];


	public function __construct()
	{

		$this->options = [
			'refresh_token' => 'Atzr|IwEBIG6cb7Pd8or1Ot54HwC3q1fOLmNiKZJCqSRBwJ3mGsR6Qist7yNvnTVtFsMixWLR-ptJBXkXaTrhMMl9Yt3iXXCppyyovfXSInVX7fvkWz3L8KLreU3nrpa2LqaTGDpgsPHDD4UmbxJXV9kaS9HvC_pZ92VrecuyaF61UuzOciSTJymSPu6EPPErzcjWAySGJUU3LeXLfABKj89yKj8l_qewuKfPsn6_Dh59ZGOgOl52VtJ4A7IC8YDn1mGoMfJMyFkZIlZgaIkwvAsrSjwS61dwkXaGBRF4QqsY3OXrxn-nwU5kN9dnnnZaphRRvivBxr0', // Aztr|...
			'client_id' => 'amzn1.application-oa2-client.0167f1a848ae4cf0aabeeb1abbeaf8cf', // App ID from Seller Central, amzn1.sellerapps.app.cfbfac4a-......
			'client_secret' => '5bf9add9576f83d33293b0e9e2ed5e671000a909f161214a77b93d26e7082765', // The corresponding Client Secret
			'region' => \ClouSale\AmazonSellingPartnerAPI\SellingPartnerRegion::$EUROPE, // or NORTH_AMERICA / FAR_EAST
			'access_key' => 'AKIAZTIHMXYBD5SRG5IZ', // Access Key of AWS IAM User, for example AKIAABCDJKEHFJDS
			'secret_key' => '4DPad08/wrtdHHP2GFInzykOl6JWLzqhkEIeZ9UR', // Secret Key of AWS IAM User
			'endpoint' => \ClouSale\AmazonSellingPartnerAPI\SellingPartnerEndpoint::$EUROPE, // or NORTH_AMERICA / FAR_EAST
			'role_arn' => 'arn:aws:iam::659829865986:role/Mosh-E-Com-SP-API-Role', // AWS IAM Role ARN for example: arn:aws:iam::123456789:role/Your-Role-Name
		];

		// 	$this->set_config();
		// }

		// public function set_config() {

		$accessToken = \ClouSale\AmazonSellingPartnerAPI\SellingPartnerOAuth::getAccessTokenFromRefreshToken(
			$this->options['refresh_token'],
			$this->options['client_id'],
			$this->options['client_secret']
		);

		$assumedRole = \ClouSale\AmazonSellingPartnerAPI\AssumeRole::assume(
			$this->options['region'],
			$this->options['access_key'],
			$this->options['secret_key'],
			$this->options['role_arn'],
		);

		$this->config = \ClouSale\AmazonSellingPartnerAPI\Configuration::getDefaultConfiguration();
		$this->config->setHost($this->options['endpoint']);
		$this->config->setAccessToken($accessToken);
		$this->config->setAccessKey($assumedRole->getAccessKeyId());
		$this->config->setSecretKey($assumedRole->getSecretAccessKey());
		$this->config->setRegion($this->options['region']);
		$this->config->setSecurityToken($assumedRole->getSessionToken());

		// $this->config = $config;

	}

	public function competitivePricing($marketplace_id, $item_type, $asins, $skus=null)
	{


		$apiInstance = new \ClouSale\AmazonSellingPartnerAPI\Api\ProductPricingApi($this->config);

		try {
            $result = $apiInstance->getCompetitivePricing($marketplace_id, $item_type, $asins, $skus);
            print_r($result);

        } catch (Exception $e) {
            echo 'Exception when calling ProductPricingApi->getCompetitivePricing: ', $e->getMessage(), PHP_EOL;
        }



		// if (isset($result->getPayload()->getAttributeSets()[0])) {
		// 	return $result->getPayload()->getAttributeSets()[0]->getTitle();
		// } else {
		// 	return 'robin';
		// }
		// return $result->getPayload()->getAttributeSets()[0]->getSmallImage();

	}
    
}
