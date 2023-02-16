<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\CommandScheduler;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;


class ScheduleCommandController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $records = CommandScheduler::query()
                ->orderBy('id', 'DESC')
                ->get()
                ->toArray();

            return Datatables::of($records)
                ->addColumn('action', function ($record) {
                    $action = '<div class="d-flex justify-content-center"><a href="/admin/scheduler/management/edit/' . $record['id'] . ' " class=" btn btn-success btn-sm " ><i class="fas fa-edit"></i> Edit</a>';
                    return $action;
                    // <a href="/admin/scheduler/management/remove/' . $record['id'] . ' " class=" ml-2 btn btn-danger btn-sm remove"><i class="fa fa-trash"></i> Remove</a>
                })
                ->make(true);
        }
        return view('admin.schedulerCommand.index');
    }

    public function FormSubmit(Request $request)
    {
        $request->validate([
            'commandName' => 'required',
            'executionTime' => 'required',
            'description' => 'required',
        ]);
        $formValue = [
            'command_name' => $request->commandName,
            'execution_time' => $request->executionTime,
            'description' => $request->description,
            'status' => $request->status == '' ? 0 : 1
        ];
        CommandScheduler::upsert($formValue, ['command_name_unique'], ['command_name', 'execution_time', 'description', 'status']);
        Cache::flush();
        CacheForCommandScheduler();
        return redirect('/admin/scheduler/management')->with("success", "Record has been inserted successfully!");
    }

    public function SchedulerEditForm($id)
    {
        $record = CommandScheduler::find($id)->toArray();
        return view('admin.schedulerCommand.index', compact('record'));
    }

    public function SchedulerFromUpdate(Request $request)
    {
        $id = $request->update_id;
        $records = [
            'command_name' => $request->commandName,
            'execution_time' => $request->executionTime,
            'description' => $request->description,
            'status' => $request->status == '' ? 0 : 1
        ];

        CommandScheduler::where('id', $id)->update($records);
        Cache::flush();
        CacheForCommandScheduler();
        return redirect('/admin/scheduler/management')->with("success", "Record has been updated successfully!");
    }

    public function SchedulerFromTrash($id)
    {
        CommandScheduler::find($id)->delete();
        return redirect('/admin/scheduler/management')->with("success", "Record has been deleted successfully!");
    }

    public function SchedulerBin(Request $request)
    {

        if ($request->ajax()) {
            $deletedRecords = CommandScheduler::onlyTrashed()->get()->toArray();
            return Datatables::of($deletedRecords)
                ->addColumn('action', function ($record) {
                    $action = '<div class="d-flex justify-content-center"><a href="/admin/scheduler/management/restore/' . $record['id'] . ' " class=" btn btn-success btn-sm restore" ><i class="fas fa-edit"></i> Restore</a>';
                    return $action;
                })
                ->make(true);
        }
        return view('admin.schedulerCommand.trash');
    }

    public function SchedulerRestore($id)
    {
        CommandScheduler::withTrashed()->find($id)->restore();
        return redirect('/admin/scheduler/management')->with("success", "Record has been restored successfully!");
    }
}
