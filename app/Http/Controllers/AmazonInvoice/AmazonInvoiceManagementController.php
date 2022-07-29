<?php

namespace App\Http\Controllers\AmazonInvoice;

use RedBeanPHP\R;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Jobs\AmazonInvoice\AmazonInvoiceUploadDO;

class AmazonInvoiceManagementController extends Controller
{

    public function index(Request $request)
    {
        // dd($data);
        if ($request->ajax()) {
            $data = DB::connection('web')->select("SELECT * FROM amazoninvoice ORDER BY status ASC");
            return DataTables::of($data)
                ->editColumn('status', function ($data) {
                    if ($data->status == 0) {
                        return 'Failed';
                    } else {
                        return 'Uploaded';
                    }
                    // return $data->status;
                })
                ->addColumn('action', function ($data) {
                    if ($data->status != 0) {
                        $action = '<div class="d-flex pl-5"><a href="/amazon/invoice/view/' . $data->id . ' " class="edit btn btn-success btn-sm" target="_blank"><i class="fas fa-eye"></i></a>';
                        return $action;
                    }
                    $action = '<div class="d-flex pl-5"><a class=" btn btn-danger btn-sm "><i class="fa fa-times" ></i></a>';
                    return $action;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
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


                    $desinationPath = $path . $fileName;

                    Storage::put($desinationPath,  file_get_contents($file));

                    $single_file = str_replace('.pdf', '', $fileName);
                    $searchPdf[] = "'$single_file'";

                    // $pdfList[] = $single_file;
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

            jobDispatchFunc($class, $job_data, 'default');
        }
        return response()->json(["message" => "All file uploaded successfully"]);
    }

    public function invoiceView($id)
    {
        $data = DB::connection('web')->select("SELECT * FROM amazoninvoice WHERE id = $id");
        $date = $data[0]->booking_date;

        $year = date('Y', strtotime($date));
        $month = date('F', strtotime($date));

        $awb = $data[0]->awb;

        $do_path = 'b2cship/' . $month . '_' . $year . '/' . $awb . '/' . $awb . '_Invoice.pdf';

        $header = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Amazon-invoice.pdf"'
        ];

        if (Storage::disk('b2cship_do_space')->exists($do_path)) {
            $url =  Storage::disk('b2cship_do_space')->get($do_path);
            return Response::make($url, 200, $header);
        }
        return "File Not Found";
    }
}
