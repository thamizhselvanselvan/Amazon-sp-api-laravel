<?php

use App\Http\Controllers\PMSPHPUnitTestController;
use Illuminate\Support\Facades\Route;

Route::get('zoho/dashboard', 'Zoho\ZohoController@Dashboard');
Route::get('zoho/getLeadsDetails/{leadId}', 'Zoho\ZohoController@getOrderDetails');
Route::get('zoho/insertZohoOrder', 'Zoho\ZohoController@addOrderItemsToZoho');

Route::get('zoho/test/{order_id}', 'Zoho\ZohoController@save');
Route::get('zoho/form', 'Zoho\ZohoController@form');
Route::get('zoho/preview', 'Zoho\ZohoController@preview');
Route::get('zoho/save', 'Zoho\ZohoController@save');

/*

404-6041032-7896329
80001172167
https://crm.zoho.com/crm/org542682384/tab/Leads/1929333000098495233

Title: Test -
Lead Name: Test -

$lead_source = array(
Amazon.ae-MBM
Amazon.in-Pram
Amazon.in-Gotech
Amazon.ae-Pram
Amazon.ae-Gotech
MBM-SAUDI
GOTECH-SAUDI
Amazon.in-Nitrous
Amazon.in-MBM
CKSHOP-Amazon.in
);


if($_REQUEST['market'] == 'nitrous' || $_REQUEST['market'] == 'in_mbm')
    $prodarray["lead_status"]='B2C Order Confirmed KYC Pending';
else
    $prodarray["lead_status"]='Order Confirmed Purchase Pending';


php artisan mosh:push-to-zoho{

 write a command that will get unprocessed orders from ord_order_update_details

 zoho array will be build and sent to zoho api using queue
 Update the zoho reference in database

}

php artisan mosh:push-to-b2cship{
 if nitrous and mbm
 then go head and make b2cship call
 update the b2c ship reference in the database

}

php artisan mosh:push-to-amazon{
 if nitrous and mbm
 and only if zoho and b2cship is created
 then go head and make sp api call to update AWB
 update the amazon request reference in the database

}




*/