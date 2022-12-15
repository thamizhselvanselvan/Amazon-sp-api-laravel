<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\ProcessManagement;
use App\Http\Controllers\Controller;

class ProcessManagementController extends Controller
{
    public function index(Request $request)
    {
        $process_managements = ProcessManagement::query()
            ->orderBy('id', 'DESC')
            ->get()
            ->toArray();
        // po($process_managements);
        if ($request->ajax()) {
            return DataTables::of($process_managements)
                ->addColumn('id', function ($process_management) {
                    $id = $process_management['id'];
                    return $id;
                })
                ->addColumn('module', function ($process_management) {
                    $module = $process_management['module'];
                    return $module;
                })
                ->addColumn('description', function ($process_management) {
                    $description = $process_management['description'] == '' ? 'Null' : $process_management['description'];
                    return $description;
                })
                ->addColumn('command_name', function ($process_management) {
                    $command_name = $process_management['command_name'];
                    return $command_name;
                })
                ->addColumn('command_start_time', function ($process_management) {
                    $command_start_time = Carbon::parse($process_management['command_start_time'])->toDayDateTimeString();
                    return $command_start_time;
                })
                ->addColumn('command_end_time', function ($process_management) {
                    $command_end_time = $process_management['command_end_time'] != '0000-00-00 00:00:00' ? Carbon::parse($process_management['command_end_time'])->toDayDateTimeString() : '0000-00-00 00:00:00';
                    return $command_end_time;
                })
                ->addColumn('processed_time', function ($process_management) {
                    $command_start_time = Carbon::parse($process_management['command_start_time']);
                    $command_end_time = $process_management['command_end_time'] == '0000-00-00 00:00:00' ? now() : Carbon::parse($process_management['command_end_time']);
                    $get_date_difference = $command_start_time->diff($command_end_time);
                    $hours = $get_date_difference->h . ' hours';
                    $minutes = $get_date_difference->i . ' minutes';
                    $processed_time = $hours . ' ' . $minutes;
                    return $processed_time;
                })
                ->addColumn('status', function ($process_management) {
                    $status = $process_management['command_end_time'] == '0000-00-00 00:00:00' ? 'Running...' : 'Completed';
                    return $status;
                })
                ->rawColumns(['id', 'module', 'description', 'command_name', 'command_start_time', 'command_end_time', 'processed_time', 'status'])
                ->make(true);
        }
        return view('ProcessManagement.index');
    }
}
