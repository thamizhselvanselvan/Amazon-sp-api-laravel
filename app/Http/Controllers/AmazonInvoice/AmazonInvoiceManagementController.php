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
use App\Models\AmazonInvoice;

class AmazonInvoiceManagementController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = AmazonInvoice::query();
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
        $path = 'AmazonInvoice/';
        foreach ($request->files as $key => $files) {

            foreach ($files as $keys => $file) {
                $file_extension = $file->getClientOriginalExtension();
                if ($file_extension == 'pdf') {

                    $fileName = $file->getClientOriginalName();
                    $desinationPath = $path . $fileName;

                    Storage::put($desinationPath,  file_get_contents($file));

                    $single_file = str_replace('.pdf', '', $fileName);
                    $searchPdf[] = "$single_file";
                }
            }
        }

        foreach ($searchPdf as $order_id) {

            $class = 'AmazonInvoice\\AmazonInvoiceUploadDO';
            $job_data['Order_id'] = $order_id;
            jobDispatchFunc($class, $job_data, 'default');
        }

        return response()->json(["message" => "All file uploaded successfully"]);
    }

    public function invoiceView($id)
    {
        $data = AmazonInvoice::where('id', $id)->get()->toArray();

        $date = $data[0]['booking_date'];
        $awb = $data[0]['awb'];

        $year = date('Y', strtotime($date));
        $month = date('F', strtotime($date));

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
