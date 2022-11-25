<?php

namespace App\Http\Controllers\Cliqnshop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CliqnshopKycController extends Controller
{
    public function kyc_index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::connection('cliqnshop')->table('users')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a href="kyc/details/' . $row->id . '/' . $row->name . ' " class="btn btn-primary btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Cliqnshop.KYC.kyc_index');
    }

    public function kyc_details($id, $name)
    { 
        $kyc = DB::connection('cliqnshop')->table('cliqnshop_kycs')->where('customer_id', $id)->get();
        if(count($kyc) == 0 ){
          return back()->with('error', ' No KYC Details Found');
        } else {
        foreach ($kyc as $KYC) {
          $kyc_customer_id = $KYC->customer_id;
            $kyc_status = $KYC->kyc_status;
            $document = $KYC->document_type;
            $front_path = $KYC->file_path_front;
            $back_path = $KYC->file_path_back;
        }
        $front_path_url = Storage::disk('cliqnshop')->temporaryUrl($front_path, '+2 minutes');
        $back_path_url = Storage::disk('cliqnshop')->temporaryUrl($back_path, '+2 minutes');


return view('Cliqnshop.KYC.kyc_details', compact('kyc', 'name', 'kyc_status', 'id', 'document', 'front_path_url', 'back_path_url'));
  } 
}

    public function kyc_status(Request $request, $id)
    {
        $kyc_status = $request->validate(
            [
                'kyc_status' => 'required',
            ]
        );
        DB::connection('cliqnshop')->table('cliqnshop_kycs')->where('customer_id', $id)->update($kyc_status);

        return back()->with('success', ' KYC Status has been updated successfully');
    }
}
