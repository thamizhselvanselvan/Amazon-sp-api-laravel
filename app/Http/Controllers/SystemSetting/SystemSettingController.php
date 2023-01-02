<?php

namespace App\Http\Controllers\SystemSetting;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting\SystemSetting;

class SystemSettingController extends Controller
{
    public function index(Request $request)
    {
        $mode = SystemSetting::where('key', 'maintenance_mode')->get();
        $maintenance_mode = isset($mode[0]['value']) ? $mode[0]['value'] : '';

        if ($request->ajax()) {
            $data = SystemSetting::get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="d-flex"><a href="/admin/system-setting/edit/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<div class="d-flex ml-1"><a href="/admin/system-setting/remove/' . $row->id . '" class="remove btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Remove</a>';

                    return $actionBtn;
                })
                ->make(true);
        }
        return view('SystemSetting.index', compact('maintenance_mode'));
    }

    public function AddSystemSetting(Request $request)
    {
        $system_data = $request->validate([
            'key' => 'required',
            'value' => 'required',
        ]);

        SystemSetting::insert($system_data);
        return redirect()->intended('/admin/system-setting')->with('success', 'Add system setting successfully.');
    }

    public function EditSystemSetting($id)
    {
        $records = SystemSetting::where('id', $id)->get();
        return view('SystemSetting.index', compact('records'));
    }

    public function UpdateSystemSetting(Request $request, $id)
    {
        $updated_data = $request->validate([
            'key' => 'required',
            'value' => 'required',
        ]);

        $update = SystemSetting::find($id);
        $update->key = $request->key;
        $update->value = $request->value;
        $update->update();
        return redirect()->intended('/admin/system-setting')->with('success', 'System setting updated successfully.');
    }

    public function DeleteSystemSetting($id)
    {
        SystemSetting::find($id)->delete();
        return redirect()->intended('/admin/system-setting')->with('danger', 'System setting has been deleted successfully.');
    }

    public function RecycleSystemSetting(Request $request)
    {
        $data = SystemSetting::onlyTrashed()->get();
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="d-flex ml-1"><a href="/admin/system-setting/restore/' . $row->id . '" class="restore btn btn-success btn-sm"><i class="far fa-trash-alt"></i> Restore</a>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('SystemSetting.bin');
    }

    public function RestoreSystemSetting($id)
    {
        SystemSetting::where('id', $id)->restore();
        return redirect()->intended('/admin/system-setting')->with('success', 'System setting has been restored successfully.');
    }
}
