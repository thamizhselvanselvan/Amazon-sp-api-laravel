<?php

namespace App\Services\BOE;

use helpers;
use DateTime;
use Exception;
use RedBeanPHP\R as R;
use App\Models\Asin_master;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Carbon;
use App\Models\Aws_credentials;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\DB;
use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Services\Config\ConfigTrait;
use SellingPartnerApi\Configuration;
use SellingPartnerApi\Api\CatalogItemsV0Api as CatalogItemsV0ApiProduct;
use SellingPartnerApi\Api\ProductPricingApi as ProductPricingApiProduct;

class BOEPdfReader
{
    public $check_table = 0;
    public $count = 0;

    public function BOEPDFReader($content, $storage_path, $company_id, $user_id)
    // public function BOEPDFReader()
    {
        $BOEPDFMaster = [];

        $content = preg_split('/[\r\n|\t|,]/', $content, -1, PREG_SPLIT_NO_EMPTY);

        $unsetKey = array_search('Page 1 of 2', $content);
        unset($content[$unsetKey]);
        $content = array_values($content);
        // dd($content);
        if ($content[0] == 'Form Courier Bill Of Entry -XIII (CBE-XIII)') {

            $BOEPDFMaster = $content;
            $current_Status_of_CBE = [];
            $courier_basic_details = [];
            $Boecheck = $BOEPDFMaster;
            $notification_details = [];
            $charge_details = [];
            $duty_details = [];
            $payment_details = [];
            $igm_details = [];
            foreach ($BOEPDFMaster as $key => $BOEPDFData) {
                if ($BOEPDFData == 'Current Status of the CBE :') {

                    $current_Status_of_CBE['CurrentStatusOfTheCbe'] = $BOEPDFMaster[$key + 1];
                } else if ($BOEPDFData == 'Courier Registration Num-') {

                    $courier_basic_details['CourierRegistrationNumber'] = $BOEPDFMaster[$key + 2];
                    $courier_basic_details['CbeNumber'] = $BOEPDFMaster[$key + 4] . $BOEPDFMaster[$key + 5];
                } else if ($BOEPDFData == 'Name of the Authorized') {

                    $name_details = '';
                    $check_key = $key + 2;
                    while ($Boecheck[$check_key] != 'Address of Authorized') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['NameOfTheAuthorizedCourier'] = $name_details;
                } else if ($BOEPDFData == 'Address of Authorized') {

                    $name_details = '';
                    $check_key = $key + 2;
                    while ($Boecheck[$check_key] != 'IGM DETAILS') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['AddressOfAuthorizedCourier'] = $name_details;
                } else if ($BOEPDFData == 'Airport of Shipment :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Country of Exportation :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['AirportOfShipment'] = $name_details;
                } else if ($BOEPDFData == 'Country of Exportation :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'HAWB Number :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['CountryOfExportation'] = $name_details;
                } else if ($BOEPDFData == 'HAWB Number :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Unique Consignment Num-') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['HawbNumber'] = $name_details;
                } else if ($BOEPDFData == 'Unique Consignment Num-') {

                    $name_details = '';
                    $check_key = $key + 2;
                    while ($Boecheck[$check_key] != 'Name of Consignor:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['UniqueConsignmentNumber'] = $name_details;
                } else if ($BOEPDFData == 'Name of Consignor:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Address of Consignor:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['NameOfConsignor'] = $name_details;
                } else if ($BOEPDFData == 'Address of Consignor:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Name of Consignee:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['AddressOfConsignor'] = $name_details;
                } else if ($BOEPDFData == 'Name of Consignee:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Address of Consignee:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['NameOfConsignee'] = $name_details;
                } else if ($BOEPDFData == 'Address of Consignee:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Import Export Code:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['AddressOfConsignee'] = $name_details;
                } else if ($BOEPDFData == 'Import Export Code:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'IEC Branch Code :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['ImportExportCode'] = $name_details;
                } else if ($BOEPDFData == 'IEC Branch Code :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Special Request:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['IecBranchCode'] = $name_details;
                } else if ($BOEPDFData == 'Special Request:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'No of Packages:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['SpecialRequest'] = $name_details;
                } else if ($BOEPDFData == 'No of Packages:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Gross Weight:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['NoOfPackages'] = $name_details;
                } else if ($BOEPDFData == 'Gross Weight:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Net Weight:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['GrossWeight'] = $name_details;
                } else if ($BOEPDFData == 'Net Weight:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Assessable Value:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }

                    $courier_basic_details['NetWeight'] = $name_details;
                } else if ($BOEPDFData == 'Assessable Value:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Duty(Rs.):') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['AssessableValue'] = $name_details;
                } else if ($BOEPDFData == 'Duty(Rs.):') {

                    $check_key = $key + 1;
                    $courier_basic_details['DutyRs'] = '';
                    if ($Boecheck[$check_key] != 'Invoice Value:') {

                        $courier_basic_details['DutyRs'] = $Boecheck[$check_key];
                    }
                } else if ($BOEPDFData == 'Invoice Value:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Case of CRN:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['InvoiceValue'] = $name_details;
                } else if ($BOEPDFData == 'Case of CRN:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'KYC Document:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['CaseOfCrn'] = $name_details;
                } else if ($BOEPDFData == 'KYC Document:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'KYC ID:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['KycDocument'] = $name_details;
                } else if ($BOEPDFData == 'KYC ID:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'State Code:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['KycNo'] = $name_details;
                } else if ($BOEPDFData == 'State Code:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Interest Amount:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['StateCode'] = $name_details;
                } else if ($BOEPDFData == 'Interest Amount:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Government / Non-') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['Interest'] = $name_details;
                } else if ($BOEPDFData == 'Government / Non-') {

                    $name_details = '';
                    $check_key = $key + 2;
                    while ($Boecheck[$check_key] != 'AD Code:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['GovernmentOrNonGov'] = $name_details;
                } else if ($BOEPDFData == 'AD Code:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'DETAILS OF CRN (if present)') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['AdCode'] = $name_details;
                } else if ($BOEPDFData == 'License Type :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'License Number :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['LicenseType'] = $name_details;
                } else if ($BOEPDFData == 'License Number :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'CTSH :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['LicenseNumber'] = $name_details;
                } else if ($BOEPDFData == 'CTSH :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'CETSH :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['Ctsh'] = $name_details;
                } else if ($BOEPDFData == 'CETSH :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Country of Origin :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['Cetsh'] = $name_details;
                } else if ($BOEPDFData == 'Country of Origin :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Description of Goods :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['CountryofOrigin'] = $name_details;
                } else if ($BOEPDFData == 'Description of Goods :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Name of Manufacturer:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['DescriptionofGoods'] = $name_details;
                } else if ($BOEPDFData == 'Name of Manufacturer:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Address of Manufacturer:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['NameofManufacturer'] = $name_details;
                } else if ($BOEPDFData == 'Address of Manufacturer:') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Number of Packages :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['AddressOfManufacturer'] = $name_details;
                } else if ($BOEPDFData == 'Number of Packages :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Marks on Packages') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['NumberofPackages'] = $name_details;
                } else if ($BOEPDFData == 'Marks on Packages') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Unit of Measure :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['MarksonPackages'] = $name_details;
                } else if ($BOEPDFData == 'Unit of Measure :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Quantity :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['UnitofMeasure'] = $name_details;
                } else if ($BOEPDFData == 'Quantity :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Invoice Number :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['Quantity'] = $name_details;
                } else if ($BOEPDFData == 'Invoice Number :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Invoice Value :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['InvoiceNumber'] = $name_details;
                } else if ($BOEPDFData == 'Invoice Value :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Unit Price :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['InvoiceValue'] = $name_details;
                } else if ($BOEPDFData == 'Unit Price :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Currency of Unit Price :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['UnitPrice'] = $name_details;
                } else if ($BOEPDFData == 'Currency of Unit Price :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Currency of Invoice :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['CurrencyofUnitPrice'] = $name_details;
                } else if ($BOEPDFData == 'Currency of Invoice :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Rate of Exchange :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['CurrencyofInvoice'] = $name_details;
                } else if ($BOEPDFData == 'Rate of Exchange :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Invoice Term :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['RateofExchange'] = $name_details;
                } else if ($BOEPDFData == 'Invoice Term :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Landing Charges :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['InvoiceTerm'] = $name_details;
                } else if ($BOEPDFData == 'Landing Charges :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Insurance :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['LandingCharges'] = $name_details;
                } else if ($BOEPDFData == 'Insurance :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Freight :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['Insurance'] = $name_details;
                } else if ($BOEPDFData == 'Freight :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Discount Amount :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['Freight'] = $name_details;
                } else if ($BOEPDFData == 'Discount Amount :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Currency of Discount :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['DiscountAmount'] = $name_details;
                } else if ($BOEPDFData == 'Currency of Discount :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Assessable Value :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['CurrencyofDiscount'] = $name_details;
                } else if ($BOEPDFData == 'Currency of Discount :') {

                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'Assessable Value :') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['CurrencyofDiscount'] = $name_details;
                } else if ($BOEPDFData == 'NOTIFICATION USED FOR THE ITEM') {

                    $name_details = '';
                    $check_key = $key + 4;
                    $offset = 0;
                    while ($Boecheck[$check_key] != 'CHARGES USED FOR THE ITEM') {
                        $notification_details[$offset]['SrNo'] = $Boecheck[$check_key++];
                        $notification_details[$offset]['NotificationNumber'] = $Boecheck[$check_key++];
                        $notification_details[$offset]['SerialNumberOfNotification'] = $Boecheck[$check_key++];
                        // $check_key += 3;
                        $offset++;
                    }
                } else if ($BOEPDFData == 'CHARGES USED FOR THE ITEM') {

                    $name_details = '';
                    $check_key = $key + 4;
                    $offset = 0;
                    while ($Boecheck[$check_key] != 'DUTY DETAILS') {
                        $charge_details[$offset]['SrNo'] = $Boecheck[$check_key++];
                        $charge_details[$offset]['ChargeType'] = $Boecheck[$check_key++];
                        $charge_details[$offset]['ChargeAmountRs'] = $Boecheck[$check_key++];
                        // $check_key += 3;
                        $offset++;
                    }
                } else if ($BOEPDFData == 'CHARGES USED FOR THE ITEM') {

                    $name_details = '';
                    $check_key = $key + 4;
                    $offset = 0;
                    while ($Boecheck[$check_key] != 'DUTY DETAILS') {
                        $charge_details[$offset]['SrNo'] = $Boecheck[$check_key++];
                        $charge_details[$offset]['ChargeType'] = $Boecheck[$check_key++];
                        $charge_details[$offset]['ChargeAmountRs'] = $Boecheck[$check_key++];
                        // $check_key += 3;
                        $offset++;
                    }
                } else if ($BOEPDFData == 'DUTY DETAILS') {

                    $name_details = '';
                    $check_key = $key + 7;
                    $offset = 0;
                    while ($Boecheck[$check_key] != 'PAYMENT DETAILS') {
                        $duty_details[$offset]['SrNo'] = $Boecheck[$check_key++];
                        $duty_details[$offset]['DutyHead'] = $Boecheck[$check_key++];
                        $duty_details[$offset]['AdValorem'] = $Boecheck[$check_key++];
                        $duty_details[$offset]['SpecificRate'] = $Boecheck[$check_key++];
                        $duty_details[$offset]['DutyForgon'] = $Boecheck[$check_key++];
                        $duty_details[$offset]['DutyAmount'] = $Boecheck[$check_key++];
                        // $check_key += 3;
                        $offset++;
                    }
                } else if ($BOEPDFData == 'PAYMENT DETAILS') {

                    $name_details = '';
                    $check_key = $key + 5;
                    $offset = 0;
                    while ($Boecheck[$check_key] != 'DECLARATION') {
                        $payment_details[$offset]['SrNo'] = $Boecheck[$check_key++];
                        $payment_details[$offset]['ChallanNumber'] = $Boecheck[$check_key++];
                        $payment_details[$offset]['TotalAmount'] = $Boecheck[$check_key++];
                        $payment_details[$offset]['ChallanDate'] = $Boecheck[$check_key++];
                        // $check_key += 3;
                        $offset++;
                    }
                } else if ($BOEPDFData == 'Time Of Arrival') {

                    $name_details = '';
                    $check_key = $key;
                    $count = 0;
                    $offset = 0;
                    $flight_name = '';
                    while ($Boecheck[$check_key] != 'Airport of Shipment :') {
                        $check_key++;
                        $count++;
                    }
                    $append = $count - 6;
                    while ($offset != $append) {
                        $flight_name .= $Boecheck[$key + $offset + 1];
                        $check_key++;
                        $offset++;
                    }
                    $igm_details['Airlines'] = $flight_name;
                    $igm_details['FlightNo'] = $Boecheck[$key + $offset + 1];
                    $igm_details['AirportOfArrival'] = $Boecheck[$key + $offset + 2];
                    $igm_details['FirstPortOfArrival'] = $Boecheck[$key + $offset + 3];
                    $igm_details['DateOfArrival'] = $Boecheck[$key + $offset + 4];
                    $igm_details['TimeOfArrival'] = $Boecheck[$key + $offset + 5];
                }
            }

            $boe_details = R::dispense('boe');
            $tables = DB::select('SHOW TABLES');
            $tableCheck = 0;
            // $testcount =0;
            if ($this->check_table == 0) {
                foreach ($tables as $table) {
                    $table = (array)($table);
                    $key = array_keys($table);
                    if ($table[$key[0]] == 'boe') {
                        $tableCheck = 1;
                    }
                }
                $this->check_table = 1;
            } else {
                $tableCheck = 1;
            }
            $dataCheck = 0;
            if ($tableCheck == 1) {

                $awb_no = $courier_basic_details['HawbNumber'];

                $selectAwb = DB::select("select hawb_number,id from boe where hawb_number = '$awb_no' AND company_id='$company_id'");
                // Log::warning($selectAwb);
                if (array_key_exists(0, $selectAwb)) {
                    $dataCheck = 1;
                }
                // R::freeze(true);
            }

            if ($dataCheck != 1) {
                //add new 
                $boe_bean_detials = $this->createbean($boe_details, $company_id, $user_id, $current_Status_of_CBE, $courier_basic_details, $igm_details, $notification_details, $charge_details, $duty_details, $payment_details);
                $date = new DateTime(date('Y-m-d'));
                $created_at = $date->format('Y-m-d');
                $boe_bean_detials->do = 0;
                $boe_bean_detials->download_file_path = $storage_path;
                $boe_bean_detials->created_at = $created_at;
                $boe_bean_detials->updated_at = $created_at;

                R::store($boe_bean_detials);
            } else {
                $id = $selectAwb[0]->id;
                //update data
                $update_bean = R::load('boe', $id);
                $update_bean->user_id = 1;

                $boe_bean_update = $this->createbean($update_bean, $company_id, $user_id, $current_Status_of_CBE, $courier_basic_details, $igm_details, $notification_details, $charge_details, $duty_details, $payment_details);
                $date = new DateTime(date('Y-m-d'));
                $updated_at = $date->format('Y-m-d');
                $boe_bean_update->do = 0;
                $boe_bean_update->download_file_path = $storage_path;
                $boe_bean_update->updated_at = $updated_at;
                R::store($update_bean);
            }
        } else {

            echo 'Invalid BOE file';
        }
    }

    public function createbean($boe_details, $company_id, $user_id, $current_Status_of_CBE, $courier_basic_details, $igm_details, $notification_details, $charge_details, $duty_details, $payment_details)
    {
        $boe_details->companyId = $company_id;
        $boe_details->userId = $user_id;
        $boe_details->currentStatusOfTheCbe = $current_Status_of_CBE['CurrentStatusOfTheCbe'];

        foreach ($courier_basic_details as $key => $courier_basic_detail) {

            $key = lcfirst($key);
            $boe_details->$key = $courier_basic_detail;
        }

        foreach ($igm_details as $boe_key => $boe) {

            $boe_key = lcfirst($boe_key);
            if ($boe_key == 'dateOfArrival') {

                $date = new DateTime(str_replace('/', '-', $boe));
                $dateformate = $date->format('Y-m-d');
                $boe_details->$boe_key = $dateformate;
            } else {
                // echo $boe;
                $boe_details->$boe_key = $boe;
            }
        }
        // exit;
        $boe_details->notificationDetails = json_encode($notification_details);
        $boe_details->chargeDetails = json_encode($charge_details);
        $boe_details->dutyDetails = json_encode($duty_details);

        if (isset($payment_details[0]['ChallanNumber'])) {
            $boe_details->challanNumber = $payment_details[0]['ChallanNumber'];
        }
        if (isset($payment_details[0]['TotalAmount'])) {

            $boe_details->totalAmount = $payment_details[0]['TotalAmount'];
        }
        if (isset($payment_details[0]['ChallanDate'])) {
            $challanDate = $payment_details[0]['ChallanDate'];

            $date = new DateTime(str_replace('/', '-', $challanDate));
            $challan_date_formate = $date->format('Y-m-d');
            $boe_details->challanDate = $challan_date_formate;

            // $boe_details->challanDate = $payment_details[0]['ChallanDate'];
        }

        $boe_details->paymentDetails = json_encode($payment_details);

        return $boe_details;
    }
}
