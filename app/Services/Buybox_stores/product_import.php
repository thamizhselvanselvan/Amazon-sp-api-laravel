<?php
namespace App\Services\buybox_stores;

use Exception;
use App\Models\Aws_credential;
use SellingPartnerApi\Endpoint;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\ReportsV20210630Api as ReportsApi;
use AmazonPHP\SellingPartner\Model\Reports\CreateReportSpecification;


class product_import
{

   public function createReport($aws_key, $country_code, $marketplace_id)
    {

        $apiInstance = new ReportsApi($this->config($aws_key, $country_code));
        $body = new CreateReportSpecification(); // \SellingPartnerApi\Model\Reports\CreateReportSpecification
        $body->setReportType("GET_MERCHANT_LISTINGS_ALL_DATA");
        $body->setMarketplaceIds([$marketplace_id]);

        try {

            $response = $apiInstance->createReport($body);

            return json_decode(json_encode($response), true);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

    public function getReports($aws_key, $country_code, $marketplace_id)
    {
        $apiInstance = new ReportsApi($this->config($aws_key, $country_code));

        $report_types = ['GET_MERCHANT_LISTINGS_ALL_DATA']; // string[] | A list of report types used to filter report schedules.
        $processing_statuses = ['DONE']; // string[] | A list of processing statuses used to filter reports.
        $marketplace_ids = [$marketplace_id]; // string[] | A list of marketplace identifiers used to filter reports. The reports returned will match at least one of the marketplaces that you specify.
        $page_size = 10; // int | The maximum number of reports to return in a single call.

        try {

            $response = $apiInstance->getReports($report_types, $processing_statuses, $marketplace_ids, $page_size);


            return json_decode(json_encode($response), true);
        } catch (Exception $e) {
            throw new Exception($e);
        }
    }
    public function config($aws_key, $country_code)
    {

        $token = '';
        $region = $this->region_code($country_code);
        $token = $this->token($aws_key);
        $endpoints = ['EU' => Endpoint::EU, 'NA' => Endpoint::NA, 'FE' => Endpoint::FE];

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
            "UK" => "EU",
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

    public function getReportDocumentByID($aws_key, $country_code, $report_document_id)
    {

        $apiInstance = new ReportsApi($this->config($aws_key, $country_code));
        $report_type = 'GET_MERCHANT_LISTINGS_ALL_DATA'; // string | The name of the document's report type.

        try {
            $response = $apiInstance->getReportDocument($report_document_id, $report_type);

            return json_decode(json_encode($response), true);
        } catch (Exception $e) {
            echo 'Exception when calling ReportsApi->getReportDocument: ', $e->getMessage(), PHP_EOL;
        }
    }
}
