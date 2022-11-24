<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class JobsManagementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::connection('web')->table('failed_jobs')
                ->orderBy('failed_at', 'desc')
                ->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('command_name', function ($data) {
                    $d = (json_decode($data->payload));
                    if (isset($d->data->commandName)) {
                        return ($d->data->commandName);
                    } else {
                        return 'NA';
                    }
                })
                ->addColumn('exception', function ($data) {
                    $actionBtn = "<a href='javascript:void(0)' id ='job_details' value = '$data->uuid' class='view btn btn-success btn-sm'><i class='fas fa-eye'></i> View Exception</a>";

                    return $actionBtn;
                })
                ->rawColumns(['display_name', 'job', 'command_name', 'Details', 'exception'])
                ->make(true);;
        }

        return view('admin.jobManagement.index');
    }

    public function exceptiondetails(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->id;
            $result = DB::connection('web')->table('failed_jobs')
                ->select('exception')
                ->where('uuid', $id)
                ->get();
            return response()->json(['success' => ' successfull', 'data' => $result]);
        }
    }
}
