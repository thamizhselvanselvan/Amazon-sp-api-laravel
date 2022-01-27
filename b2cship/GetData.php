<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

$data = $_POST['data'];
// $data ='US10000053';

$datas = preg_split('/[\r\n| |:|,]/', $data, -1, PREG_SPLIT_NO_EMPTY);

$newArray = [];
$dataArray= [];
$count = 0;


foreach($datas as $value)
{
 $dataArray[$count] = TrackingDetais($value);  
    $count++;
}

echo json_encode($dataArray);

 function TrackingDetais( $data)
{


$url = "https://uat-api.b2cship.us/PacificAmazonAPI.svc/TrackingAmazon";

$xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
<AmazonTrackingRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:noNamespaceSchemaLocation="AmazonTrackingRequest.xsd">
<Validation>
<UserID>Amazon</UserID>
<Password>AcZmraDzLoxA4NxLUcyrWnSiEaXxRQkfJ9B5hCbiK5M=</Password>
</Validation>
<APIVersion>1.0</APIVersion>
<TrackingNumber>'.$data.' </TrackingNumber>
</AmazonTrackingRequest>';

//setting the curl headers
$headers = array(
    "Content-type: text/plain ;charset=\"utf-8\"",
    "Accept: application/plain",
);

try{

    $ch = curl_init();

    //setting the curl options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS,  $xmlRequest);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $data = curl_exec($ch);
    $ofset= 0;
    
   
    //convert the XML result into array
    if($data === false){
        $error = curl_error($ch);
        echo $error;
        die('error occured');
    }else{
        $data = json_decode(json_encode(simplexml_load_string($data)), true);
        
        $trackingNumber= $data['PackageTrackingInfo']['TrackingNumber'];
        $city= $data['PackageTrackingInfo']['PackageDestinationLocation']['City'];
        $PostalCode= $data['PackageTrackingInfo']['PackageDestinationLocation']['PostalCode'];
        $CountryCode= $data['PackageTrackingInfo']['PackageDestinationLocation']['CountryCode'];

        //  echo json_encode($trackingNumber." ".$city." ".$PostalCode." ".$CountryCode);
        $key1= null;
        foreach($data['PackageTrackingInfo']['TrackingEventHistory']['TrackingEventDetail'] as $key1=>$value1){
            if(gettype($key1)== 'integer'){ 
             // foreach($data['PackageTrackingInfo']['TrackingEventHistory']['TrackingEventDetail'][$key1] as $key2=>$value2)
                foreach($value1 as $key2 => $value2){ 
                    if(!is_array($value2) && $key2!= 'EventStatus')
                    {
                        $newArray[$key1+1][$ofset][$key2] = $value2;
                        $ofset++;
                    }
                        $eventCity= $data['PackageTrackingInfo']['TrackingEventHistory']['TrackingEventDetail'][$key1]['EventLocation']['City'];
                }
                    $newArray[$key1+1][$ofset]['EventCity'] = $eventCity; 
                    $ofset= 0;
            }else{
                    
                    $eventCity= $data['PackageTrackingInfo']['TrackingEventHistory']['TrackingEventDetail']['EventLocation']['City'];
                    $eventReason= $data['PackageTrackingInfo']['TrackingEventHistory']['TrackingEventDetail']['EventReason'];
                    $eventDateTime= $data['PackageTrackingInfo']['TrackingEventHistory']['TrackingEventDetail']['EventDateTime'];
                    $newArray[1][0]['EventReason'] = $eventReason;
                    $newArray[1][1]['EventDateTime'] = $eventDateTime;
                    $newArray[1][2]['EventCity'] = $eventCity;
        
            }
        }
        
        $newArray[0][0]['TrackingNumber']=$trackingNumber;
        $newArray[0][1]['City']=$city;
        $newArray[0][2]['PostalCode']=$PostalCode;
        $newArray[0][3]['CountryCode']=$CountryCode;

    //    echo json_encode($newArray);
          return ($newArray);

    }

    curl_close($ch);

}catch(Exception  $e){
    echo 'Message: '.$e->getMessage();
    die("Error");
}
}
?>