<?php

namespace App\Http\Controllers\shipntrack\SMSA;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\ShipNTrack\SMSA\SmsaTrackings;

use function Clue\StreamFilter\fun;

class SmsaExperessController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SmsaTrackings::orderBy('date', 'desc')->get()->unique('awbno');
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $action = "<a href='/shipntrack/smsa/moredetails/" . $data['awbno'] . "' class='' target='_blank'>More Details</a>";
                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('shipntrack.Smsa.index');
    }

    public function PacketMoreDetails($awbNo)
    {
       $result = SmsaTrackings::where('awbno', $awbNo)->orderBy('date', 'desc')->get();
    //    dd($result);
    return view('shipntrack.Smsa.packetDetails', compact('result'));
    }
    public function uploadAwb()
    {
        return view('shipntrack.Smsa.upload');
    }

    public function GetTrackingDetails(Request $request)
    {
        $request->validate([
            'smsa_awbNo' => 'required|min:10',
        ]);

        $tracking_id = $request->smsa_awbNo;

        $datas = preg_split('/[\r\n| |:|,]/', $tracking_id, -1, PREG_SPLIT_NO_EMPTY);
        $datas = array_unique($datas);

        foreach ($datas as $awbNo) {
            $tracking_details = [];
            $details = $this->TrackingResponse($awbNo);
            foreach ($details as $key => $value) {

                $tracking_details[] = [

                    "awbno" => $value['awbNo'],
                    "date" => date('Y-m-d H:i:s', strtotime($value['Date'])),
                    "activity" => $value['Activity'],
                    "details" => $value['Details'],
                    "location" => $value['Location']
                ];
            }

            SmsaTrackings::upsert(
                $tracking_details,
                ['awbno_date_activity_unique'],
                ['awbno', 'date', 'activity', 'details', 'location']
            );
        }
        // retrun v
        return redirect()->intended('/shipntrack/smsa')->with('success', 'Tracking Details Saved');
    }

    public function TrackingResponse($awbNo)
    {
        $password = 'Bom@7379';
        $url = "http://track.smsaexpress.com/SECOM/SMSAwebService.asmx";

        $xmlRequest = "<?xml version='1.0' encoding='utf-8'?>
        <soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
            <soap:Body>
                <getTracking xmlns='http://track.smsaexpress.com/secom/'>
                    <awbNo>$awbNo</awbNo>
                    <passkey>$password</passkey>
                </getTracking>
            </soap:Body>
        </soap:Envelope>";

        $headers = array(
            'Content-type: text/xml',
        );

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

        $plainXML = $this->mungXML(trim($data));
        $arrayResult = json_decode(json_encode(SimpleXML_Load_String($plainXML, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        $arrayResult = $arrayResult['soap_Body']['getTrackingResponse']['getTrackingResult']['diffgr_diffgram'];
        if (array_key_exists('NewDataSet', $arrayResult)) {

            return $arrayResult['NewDataSet']['Tracking'];
        } else {

            // echo "Invalid Awb No. ". $awbNo;
        }
    }
    public function mungXML($xml)
    {
        $obj = SimpleXML_Load_String($xml);
        if ($obj === FALSE) return $xml;

        // GET NAMESPACES, IF ANY
        $nss = $obj->getNamespaces(TRUE);
        if (empty($nss)) return $xml;

        // CHANGE ns: INTO ns_
        $nsm = array_keys($nss);
        foreach ($nsm as $key) {
            // A REGULAR EXPRESSION TO MUNG THE XML
            $rgx
                = '#'               // REGEX DELIMITER
                . '('               // GROUP PATTERN 1
                . '\<'              // LOCATE A LEFT WICKET
                . '/?'              // MAYBE FOLLOWED BY A SLASH
                . preg_quote($key)  // THE NAMESPACE
                . ')'               // END GROUP PATTERN
                . '('               // GROUP PATTERN 2
                . ':{1}'            // A COLON (EXACTLY ONE)
                . ')'               // END GROUP PATTERN
                . '#'               // REGEX DELIMITER
            ;
            // INSERT THE UNDERSCORE INTO THE TAG NAME
            $rep
                = '$1'          // BACKREFERENCE TO GROUP 1
                . '_'           // LITERAL UNDERSCORE IN PLACE OF GROUP 2
            ;
            // PERFORM THE REPLACEMENT
            $xml =  preg_replace($rgx, $rep, $xml);
        }

        return $xml;
    } // End :: mungXML()

}
