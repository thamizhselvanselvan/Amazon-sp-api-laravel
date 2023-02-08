<?php

namespace App\Http\Controllers\Cliqnshop;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CliqnshopKycController extends Controller
{
    public function kyc_index(Request $request)
    {

    
        $data = DB::connection('cliqnshop')->table('users')
            ->leftjoin('cns_kycs as kyc', function ($query) {
                $query->on('kyc.customer_id', '=', 'users.id');
            })
            ->get();
         
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()

                ->editColumn('kyc_status', function ($data) {
                    $val = $data->kyc_status;
                    if ($val == '') {
                        return 'Pending For Approval';
                    } else if ($val == 1) {
                        return 'KYC Accepted';
                    } else {
                        return 'KYC Rejected';
                    }
                })

                ->editColumn('rejection_reason', function ($data) {
                    return $data->rejection_reason;
                })

                ->addColumn('action', function ($data) {
                    $id = $data->customer_id;
                // return "<a href='javascript:void(0)' id='kyc_aprove' value ='$id' 'class='btn btn-success btn-sm'><i class='fa fa-check' aria-hidden='true'></i> view Approve</a>";
                return       "<div class='d-flex'><a href='javascript:void(0)' id='kyc_aprove' value ='$id' class='edit btn btn-success btn-sm'><i class='fas fa-check'></i> view Approve</a>";
                }) 
                   
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Cliqnshop.KYC.kyc_index');
    }

    public function kyc_details(Request $request)
    {

        if ($request->ajax()) {
            $id = $request->id;
            $kyc = DB::connection('cliqnshop')->table('users')
                ->join('cns_kycs as kyc', function ($query) {
                    $query->on('kyc.customer_id', '=', 'users.id');
                })
                ->where('kyc.customer_id', $id)->get();
            if (count($kyc) == 0) {
                return 'no kyc found';
            } else {
                 foreach ($kyc as $KYC) {
                $document = $KYC->document_type;
                $front_path = $KYC->file_path_front;
                $back_path = $KYC->file_path_back;
            }
                $front_path_url = Storage::disk('cliqnshop')->temporaryUrl($front_path, '+2 minutes');
                $back_path_url = Storage::disk('cliqnshop')->temporaryUrl($back_path, '+2 minutes');
          
                $data = [

                    'kyc' => $kyc,
                    'front_path_url' => $front_path_url,
                    'back_path_url' => $back_path_url,
                ];
            }
            return $data;
        }
    }

    public function kyc_status(Request $request)
    {

        
     $date = Carbon::now();
       $reject_reason = $request->rea;
       $id = $request->id;
       $status = $request->status;
       
            $kyc_status = [
            'kyc_status' =>   $status,
            'rejection_reason' =>$reject_reason,
            'kyc_aproved_date' => $date ,
            ];

        DB::connection('cliqnshop')->table('cns_kycs')->where('customer_id', $id)->update($kyc_status);

        return back()->with('success', 'KYC Status has  updated successfully');
    }
}
