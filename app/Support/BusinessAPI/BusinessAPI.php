<?php 

namespace App\Support\BusinessAPI;

use AmazonBusinessApi\Endpoint;
use AmazonBusinessApi\Configuration;

class BusinessAPI {

    public static function config() {

        //date_default_timezone_set('Asia/Jakarta');

        return new Configuration([
            'lwaClientId' => 'amzn1.application-oa2-client.6c64a78c8f214ae1999ba6725aa68bd5',
            'lwaClientSecret' => '80b1db8f2e3ae4b755bd50a0bcc21228694381e6a35b178efdb43799ccedd1ae',
            'awsAccessKeyId' => 'AKIARVGPJZCJHLW5MH63',
            'awsSecretAccessKey' => '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',
            'lwaRefreshToken' => 'Atzr|IwEBIGBnur8ckY5T1BPQTCvdM-LDHEQ1rqpBrKNAy44n_6HQKhz2DstKC0hOFUkWowyUN64k99Fj6BVJCR0nXTXn_MD6dLaoAHtsKQW6_VDStqyR8FImcHm94A6SLuGukK6qHNF0-4c9hY3nx03jBHQ9K4TOLg55O-vDTkr6T6WCn4q0hcJ05e4324qZdOBvoAzaJb9NFQkQyjX1ken_o9Wf55aZzbbgPcRmxqKy35o9k2HdcLFFnr9Qj737MMXQQ0AUT8f5YhMg7cNF6DM2VIX43wK3WMG6JwGjkszmL-4TWKbv4bRjRZXHLs1WkgUmwMGyH_0Lr6bgtQbvj76m9WQGg11H',
            // If you're not working in the North American marketplace, change
            // this to another endpoint from lib/Endpoint.php
            'endpoint' => Endpoint::NA,
        ]);


    }

    

}