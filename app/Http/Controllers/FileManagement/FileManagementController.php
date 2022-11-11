<?php

namespace App\Http\Controllers\FileManagement;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\FileManagement;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class FileManagementController extends Controller
{
    public function index()
    {
        $file_info = [];
        $type_replace = ['_ASIN_DESTINATION', '_ASIN_SOURCE', 'CATALOG_', 'PRICE_'];
        $module_replace = ['_US_1', '_US_2', '_US_3', '_IN_1', '_IN_2', '_IN_3', '_IN', '_US', '_EXPORT'];
        $file_managements = FileManagement::select('user_id', 'type', 'module', 'file_name', 'command_start_time', 'command_end_time')->get()->toArray();
        foreach ($file_managements as $file_management) {

            $user_name = User::where('id', $file_management['user_id'])->get('name')->toArray();
            $user = $user_name[0]['name'];
            $type = str_replace($type_replace, '', $file_management['type']);
            $module = str_replace($module_replace, '', $file_management['module']);
            $file_name = $file_management['file_name'];
            $start_time = Carbon::parse($file_management['command_start_time'])->toDayDateTimeString();
            $end_time = $file_management['command_end_time'] != '0000-00-00 00:00:00' ? Carbon::parse($file_management['command_end_time'])->toDayDateTimeString() : '0000-00-00 00:00:00';
            $process = $file_management['command_end_time'] == '0000-00-00 00:00:00' ? 'Processing...' : 'Processed';

            $file_info[] = [
                'user_name'     => $user,
                'type'          => $type,
                'module'        => $module,
                'start_time'    => $start_time,
                'end_time'      => $end_time,
                'process'       => $process,
            ];
        }

        return view('FileManagement.index', compact('file_info'));
    }
}
