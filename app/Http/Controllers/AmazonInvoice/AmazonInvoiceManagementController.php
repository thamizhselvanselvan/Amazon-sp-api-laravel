<?php

namespace App\Http\Controllers\AmazonInvoice;

use RedBeanPHP\R;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Jobs\AmazonInvoice\AmazonInvoiceUploadDO;
use Illuminate\Support\Facades\Storage;

class AmazonInvoiceManagementController extends Controller
{

    public function index()
    {
        return view('amazonInvoice.index');
    }

    public function uploadInvoice()
    {
        return view('amazonInvoice.uploadInvoice');
    }

    public function invoiceSave(Request $request)
    {
        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');

        if (!R::testConnection('web', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password)) {
            R::addDatabase('web', "mysql:host=$host;dbname=$dbname;port=$port", $username, $password);
            R::selectDatabase('web');
        }

        $path = 'AmazonInvoice/';
        foreach ($request->files as $key => $files) {

            foreach ($files as $keys => $file) {
                $file_extension = $file->getClientOriginalExtension();
                if ($file_extension == 'pdf') {

                    $fileName = $file->getClientOriginalName();
                    // $fileName = uniqid() . ($fileName);
                    $desinationPath = $path . $fileName;
                    Storage::put($desinationPath,  file_get_contents($file));
                    $single_file = str_replace('.pdf', '', $fileName);
                    // $pdfList[] = $single_file;
                    $searchPdf[] = "'$single_file'";
                }
            }
        }

        $whereIn = implode(',', $searchPdf);
        $data = DB::connection('b2cship')
            ->select("SELECT AWBNo, RefNo, BookingDate FROM Packet
                    WHERE RefNo IN ($whereIn) 
                ");

        foreach ($data as $key => $value) {

            $job_data = [];
            $amazon_invoice = R::dispense('amazoninvoice');
            $amazon_invoice->awb = $value->AWBNo;
            $amazon_invoice->amazon_order_identifier = $value->RefNo;
            $amazon_invoice->booking_date = $value->BookingDate;
            $amazon_invoice->status  = '0';
            $amazon_invoice->created_at = now();
            R::store($amazon_invoice);

            $job_data['AwbNo'] = $value->AWBNo;
            $job_data['Order_id'] = $value->RefNo;
            $job_data['Date'] = $value->BookingDate;

            $class = 'AmazonInvoice\\AmazonInvoiceUploadDO';
        
            jobDispatchFunc($class, $job_data,'amazonInvoice');   

        }
        
        return response()->json(["message" => "All file uploaded successfully"]);
    }
}
