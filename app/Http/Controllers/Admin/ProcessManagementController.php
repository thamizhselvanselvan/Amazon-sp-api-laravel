<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ProcessManagementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $process_managements = ProcessManagement::query()
                ->orderBy('id', 'DESC');
            return DataTables::of($process_managements)
                ->editColumn('command_start_time', function ($process_management) {
                    $command_start_time = Carbon::parse($process_management['command_start_time'])->toDayDateTimeString();
                    return $command_start_time;
                })
                ->editColumn('command_end_time', function ($process_management) {
                    $command_end_time = $process_management['command_end_time'] != '0000-00-00 00:00:00' ? Carbon::parse($process_management['command_end_time'])->toDayDateTimeString() : '0000-00-00 00:00:00';
                    return $command_end_time;
                })
                ->editColumn('processed_time', function ($process_management) {
                    $command_start_time = Carbon::parse($process_management['command_start_time']);
                    $command_end_time = $process_management['command_end_time'] == '0000-00-00 00:00:00' ? now() : Carbon::parse($process_management['command_end_time']);
                    $get_date_difference = $command_start_time->diff($command_end_time);
                    // $hours = $get_date_difference->h . ' hours';
                    $minutes = $get_date_difference->i . ' minutes';
                    $seconds = $get_date_difference->s . ' seconds';
                    $processed_time = $minutes . ' ' . $seconds;
                    return $processed_time;
                })
                ->editColumn('status', function ($process_management) {
                    $status = $process_management['command_end_time'] == '0000-00-00 00:00:00' ? 'Running...' : 'Completed.';
                    return $status;
                })
                // ->rawColumns(['command_name', 'command_start_time', 'command_end_time', 'processed_time', 'status'])
                ->make(true);
        }
        return view('ProcessManagement.index');
    }
}
