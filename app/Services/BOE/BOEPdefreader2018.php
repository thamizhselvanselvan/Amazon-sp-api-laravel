<?php

namespace App\Services\BOE;

use DateTime;
use RedBeanPHP\R as R;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\elementType;

class BOEPdefreader2018
{
    public $check_table = 0;
    public $count = 0;


    public function
    BOEPDFReaderold($content, $storage_path, $company_id, $user_id)
    // BOEPDFReaderold()
    {

        // $company_id = 1;
        // $user_id = 1;
        // $storage_path  = '';
        // $pdfParser = new Parser();
        // $second_page = 1;
        // $path = 'D:\BOE\957341067.pdf';
        // //  $path = 'D:\BOE\957344276.pdf';
        // $path = 'D:\BOE\Test\957302469.pdf';
        // $path = 'D:\BOE\wd\957376660.pdf';


        // $pdfParser = new Parser();
        // $pdf = $pdfParser->parseFile($path);
        // $content = $pdf->getText();

        // echo $path;
        $content = preg_split('/[\r\n|\t|,]/', $content, -1, PREG_SPLIT_NO_EMPTY);
        //   dd($content);
  

          // Log::alert($maxkey);
          
        foreach ($content as $key => $data) {
            if ($data == 'Page1of2' || $data == 'Page 1 of 2') {
                unset($content[$key]);
            } else if ($data == 'NOTIFICATION USED FOR THE ITEM') {
                
                if ($content[$key + 1] != 'Sr.No.') {
                    unset($content[$key + 1]);
                }
            }
        }
        
        
        $content = array_values($content);
        $maxkey = max(array_keys($content));
        



        if ($content[0] == "Form Courier Bill Of Entry -XIII (CBE-XIII)") {
            $BOEPDFMasterold = $content;

            $courier_basic_details = [];
            $Boecheck = $BOEPDFMasterold;
            $notification_details = [];
            $charge_details = [];
            $duty_details = [];
            $payment_details = [];
            $igm_details = [];
            $data = [];
            foreach ($content as $key => $BOEPDFData) {
                if ($BOEPDFData == 'Current Status of the CBE :') {

                    $courier_basic_details['CurrentStatusOfTheCbe'] = $BOEPDFMasterold[$key + 1];
                } else if ($BOEPDFData == 'Courier Registration Num-') {
                    $courier_basic_details['CourierRegistrationNumber'] = $BOEPDFMasterold[$key + 2];
                    $courier_basic_details['CbeNumber'] = $BOEPDFMasterold[$key + 4] . $BOEPDFMasterold[$key + 5];
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
                    while ($Boecheck[$check_key] != 'IGM DETAILS' && $Boecheck[$check_key] != 'Airlines:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['AddressOfAuthorizedCourier'] = $name_details;
                    // boe_loop($key, $Boecheck, 'IGM DETAILS', $courier_basic_details, 'AddressofAuthorized');

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
                } else if ($BOEPDFData == 'Airlines:') {
                    boe_loop($key, $Boecheck, 'Flight Number :', $courier_basic_details, 'Airlines');
                } else if ($BOEPDFData == 'Flight Number :') {

                    boe_loop($key, $Boecheck, 'Airport of Arrival :', $courier_basic_details, 'FlightNo');
                } else if ($BOEPDFData == 'Airport of Arrival :') {

                    boe_loop($key, $Boecheck, 'First Port of Arrival', $courier_basic_details, 'AirportOfArrival');
                } else if ($BOEPDFData == 'First Port of Arrival') {
                    $name_details = '';
                    $check_key = $key + 2;
                    while ($Boecheck[$check_key] != 'Date of Arrival:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['FirstPortOfArrival'] = $name_details;
                } else if ($BOEPDFData == 'Date of Arrival:') {

                    boe_loop($key, $Boecheck, 'Time of Arrival :', $courier_basic_details, 'DateOfArrival');
                } else if ($BOEPDFData == 'Time of Arrival :') {

                    boe_loop($key, $Boecheck, 'Airport of Shipment :', $courier_basic_details, 'TimeOfArrival');
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
                    while ($Boecheck[$check_key] != 'GSTIN Type:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['CaseOfCrn'] = $name_details;
                } else if ($BOEPDFData == 'GSTIN Type:') {
                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'GSTIN Number:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['GSTINType'] = $name_details;
                } else if ($BOEPDFData == 'GSTIN Number:') {
                    $name_details = '';
                    $check_key = $key + 1;
                    while ($Boecheck[$check_key] != 'State Code:') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['GSTINNumber'] = $name_details;
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
                    while ($Boecheck[$check_key] != 'DETAILS OF CRN (if present)') {
                        $name_details .= $Boecheck[$check_key];
                        $check_key++;
                    }
                    $courier_basic_details['Interest'] = $name_details;
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
                    $courier_basic_details['LicenseNumbere'] = $name_details;
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

                    boe_loop($key, $Boecheck, 'Invoice Term :', $courier_basic_details, 'RateofExchange');
                } else if ($BOEPDFData == 'Invoice Term :') {

                    boe_loop($key, $Boecheck, 'Landing Charges :', $courier_basic_details, 'InvoiceTerm');
                } else if ($BOEPDFData == 'Landing Charges :') {

                    boe_loop($key, $Boecheck, 'Insurance :', $courier_basic_details, 'LandingCharges');
                } else if ($BOEPDFData == 'Insurance :') {

                    boe_loop($key, $Boecheck, 'Freight :', $courier_basic_details, 'Insurance');
                } else if ($BOEPDFData == 'Freight :') {

                    boe_loop($key, $Boecheck, 'Discount Amount :', $courier_basic_details, 'Freight');
                } else if ($BOEPDFData == 'Discount Amount :') {

                    boe_loop($key, $Boecheck, 'Currency of Discount :', $courier_basic_details, 'DiscountAmount');
                }
                 else if ($BOEPDFData == 'Currency of Discount :') {

                    boe_loop($key, $Boecheck, 'Assessable Value :', $courier_basic_details, 'CurrencyofDiscount');
                  
                } 
                // else if ($BOEPDFData == 'Assessable Value :') {
                //     boe_loop($key, $Boecheck, 'Duty(Rs.):', $courier_basic_details, 'AssessableValue');
                // } 
                else if ($BOEPDFData == 'NOTIFICATION USED FOR THE ITEM') {    
                    $val = $key + 1;
                    if (array_key_exists($val, $content)) {

                        $check_key = $key + 4;
                        $offset = 0;
                        $count = 0;
                        while ($Boecheck[$check_key] != 'CHARGES USED FOR THE ITEM') {
                          $count++;
                          $check_key++;
                        }

                        $check_key = $key + 4;

                        while ($Boecheck[$check_key] != 'CHARGES USED FOR THE ITEM') {
                            if($count < 3)
                            {
                                $notification_details[$offset]['SrNo'] = '1';
                                $notification_details[$offset]['NotificationNumber'] = $Boecheck[$check_key++];
                                $notification_details[$offset]['SerialNumberOfNotification'] = $Boecheck[$check_key++];
                            }
                            else
                            {
                                $notification_details[$offset]['SrNo'] = $Boecheck[$check_key++];
                                $notification_details[$offset]['NotificationNumber'] = $Boecheck[$check_key++];
                                $notification_details[$offset]['SerialNumberOfNotification'] = $Boecheck[$check_key++];
                            }
                            $offset++;
                        }
                    }
                   
                } else if ($BOEPDFData == 'CHARGES USED FOR THE ITEM') {
                    $check_key = $key + 4;
                    $offset = 0;
                    
                    while ($Boecheck[$check_key] != 'DUTY DETAILS') {
                        $charge_details[$offset]['SrNo'] = $Boecheck[$check_key++];
                        $charge_details[$offset]['ChargeType'] = $Boecheck[$check_key++];
                        $charge_details[$offset]['ChargeAmountRs'] = $Boecheck[$check_key++];
                        $offset++;
                    }
                } else if ($BOEPDFData == 'DUTY DETAILS') {
                    $check_key = $key + 7;
                    $offset = 0;
                    while ($Boecheck[$check_key] != 'PAYMENT DETAILS') {
                        $duty_details[$offset]['SrNo'] = $Boecheck[$check_key++];
                        $duty_details[$offset]['DutyHead'] = $Boecheck[$check_key++];
                        $duty_details[$offset]['AdValorem'] = $Boecheck[$check_key++];
                        $duty_details[$offset]['SpecificRate'] = $Boecheck[$check_key++];
                        $duty_details[$offset]['DutyForgon'] = $Boecheck[$check_key++];
                        $duty_details[$offset]['DutyAmount'] = $Boecheck[$check_key++];
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
                        
                        $offset++;
                    }
                    
                }
              else  if ($BOEPDFData == "DECLARATION") {
                  
                  $data[] = [
                      'courier_basic_details' => $courier_basic_details,
                      'notification_details' => $notification_details,
                      'charge_details' => $charge_details,
                      'duty_details' => $duty_details,
                      'payment_details' => $payment_details,
                      'igm_details' => $igm_details
                    ];
                }
              
            }
         
            foreach ($data as $boe_details) {
                $courier_basic_details = $boe_details['courier_basic_details'];
                $notification_details = $boe_details['notification_details'];
                $charge_details = $boe_details['charge_details'];
                $duty_details = $boe_details['duty_details'];
                $payment_details = $boe_details['payment_details'];
                $igm_details = $boe_details['igm_details'];

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
                    if (array_key_exists(0, $selectAwb)) {
                        $dataCheck = 1;
                    }
                }

                
                if ($dataCheck != 1) {
                    //add new 
                    $boe_bean_detials = $this->createbean($boe_details, $company_id, $user_id, $courier_basic_details, $igm_details, $notification_details, $charge_details, $duty_details, $payment_details);
                    $date = new DateTime(date('Y-m-d'));
                    $created_at = $date->format('Y-m-d');
                    $boe_bean_detials->do = 0;
                    $boe_bean_detials->download_file_path = $storage_path;
                    $boe_bean_detials->created_at = $created_at;
                    $boe_bean_detials->updated_at = $created_at;

                    R::store($boe_bean_detials);
                } else {
                    // //update data

                    $id = $selectAwb[0]->id;
                    $update_bean = R::load('boe', $id);
                    $update_bean->user_id = 1;

                    $boe_bean_update = $this->createbean($update_bean, $company_id, $user_id, $courier_basic_details, $igm_details, $notification_details, $charge_details, $duty_details, $payment_details);
                    $date = new DateTime(date('Y-m-d'));
                    $updated_at = $date->format('Y-m-d');
                    $boe_bean_update->do = 0;
                    $boe_bean_update->download_file_path = $storage_path;
                    $boe_bean_update->updated_at = $updated_at;
                    R::store($update_bean);
                }
            }
        } else {
            return false;
        }
    }
    public function createbean($boe_details, $company_id, $user_id, $courier_basic_details, $igm_details, $notification_details, $charge_details, $duty_details, $payment_details)
    {


        $boe_details->companyId = $company_id;
        $boe_details->userId = $user_id;

        $boe_details->currentStatusOfTheCbe = $courier_basic_details['CurrentStatusOfTheCbe'];


        foreach ($courier_basic_details as $key => $courier_basic_detail) {

            $key = lcfirst($key);
            if ($key == 'dateOfArrival') {
                $dateformate = $this->dateFormate($courier_basic_detail);
                $boe_details->$key = $dateformate;
            } else {

                $boe_details->$key = $courier_basic_detail;
            }
        }

        if ($igm_details != '') {
            foreach ($igm_details as $boe_key => $boe) {

                $boe_key = lcfirst($boe_key);
                if ($boe_key == 'dateOfArrival') {

                    $dateformate = $this->dateFormate($boe);
                    $boe_details->$boe_key = $dateformate;
                } else {

                    $boe_details->$boe_key = $boe;
                }
            }
        }

        $boe_details->notificationDetails = json_encode($notification_details);
        $boe_details->chargeDetails = json_encode($charge_details);
        $boe_details->dutyDetails = json_encode($duty_details);


        $boe_details->paymentDetails = json_encode($payment_details);

        return $boe_details;
    }

    public function dateFormate($date)
    {
        $date = new DateTime(str_replace('/', '-', $date));
        $dateformate = $date->format('Y-m-d');
        return $dateformate;
    }
}
