<?php

namespace App\Http\Controllers\FileManagement;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\FileManagement;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class FileManagementController extends Controller
{
    public function index(Request $request)
    {

        $file_managements = FileManagement::query()
            ->select('id', 'user_id', 'type', 'module', 'file_name', 'command_start_time', 'command_end_time')
            ->orderBy('id', 'DESC')
            ->get()
            ->toArray();

        if ($request->ajax()) {

            return DataTables::of($file_managements)
                ->addColumn('id', function ($file_management) {
                    $id = $file_management['id'];
                    return $id;
                })
                ->addColumn('user_name', function ($file_management) {
                    $user_name = User::where('id', $file_management['user_id'])->get('name')->toArray();
                    $user = $user_name[0]['name'] ?? 'Auto';
                    return $user;
                })
                ->addColumn('type', function ($file_management) {
                    $type_replace = ['_ASIN_INTO_BUYBOX', '_ASIN_DESTINATION', '_ASIN_SOURCE', 'CATALOG_', 'PRICE_', '_INVOICE', '_ORDER', '_LABEL'];
                    $type = str_replace($type_replace, '', $file_management['type']);
                    return $type;
                })
                ->addColumn('module', function ($file_management) {
                    // $module_replace = [',', '_US_1', '_US_2', '_US_3', '_IN_1', '_IN_2', '_IN_3', '_IN', '_US', '_EXPORT', 'US_1', 'US_2', 'US_3', 'US', '_5', '_6', '_7', '_9', '_10', '_11', '_13', '_14', '_15', '_18', '_20', '_21', '_22', '_27', '_29', '_35', '_42', '_44',];
                    $module_replace = [',', '_', '-EXPORT-', '_5', '_6', '_7', '_9', '_10', '_11', '_13', '_14', '_15', '_18', '_20', '_21', '_22', '_27', '_29', '_35', '_42', '_44',];
                    $module = str_replace($module_replace, '-', $file_management['module']);
                    return $module;
                })
                ->addColumn('start_time', function ($file_management) {
                    $start_time = Carbon::parse($file_management['command_start_time'])->toDayDateTimeString();
                    return $start_time;
                })
                ->addColumn('end_time', function ($file_management) {
                    $end_time = $file_management['command_end_time'] != '0000-00-00 00:00:00' ? Carbon::parse($file_management['command_end_time'])->toDayDateTimeString() : '0000-00-00 00:00:00';
                    return $end_time;
                })
                ->addColumn('processed_time', function ($file_management) {
                    $start_time =  Carbon::parse($file_management['command_start_time']);
                    $end_time = $file_management['command_end_time'] == '0000-00-00 00:00:00' ? now() : Carbon::parse($file_management['command_end_time']);
                    $date_difference = $start_time->diff($end_time);
                    $hour = $date_difference->h . ' hours';
                    $sec = $date_difference->i . ' minutes';
                    $processed_time = $hour . ' ' . $sec;
                    return $processed_time;
                })
                ->addColumn('status', function ($file_management) {
                    $process = $file_management['command_end_time'] == '0000-00-00 00:00:00' ? 'Processing...' : 'Processed';
                    return $process;
                })
                ->rawColumns(['id', 'user_name', 'type', 'module', 'start_time', 'end_time', 'processed_time', 'status'])
                ->make(true);;
        }
        return view('FileManagement.index');
    }
}
