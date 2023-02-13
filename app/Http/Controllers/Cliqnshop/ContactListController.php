<?php

namespace App\Http\Controllers\Cliqnshop;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ContactListController extends Controller
{
    public function contactlist(Request $request)
    {
        $data = DB::connection('cliqnshop')->table('cns_contacts')->orderBy('id','desc')->get();

        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $mail = $data->email;
                    return  "<div class='d-flex'><a href='mailto:$mail' class='edit btn btn-success btn-sm'><i class='fas fa-envelope'></i> Send email</a>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('Cliqnshop.contact.contact_list');
    }
}
