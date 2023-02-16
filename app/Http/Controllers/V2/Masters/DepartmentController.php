<?php

namespace  App\Http\Controllers\V2\Masters;

use App\Http\Controllers\Controller;
use App\Models\V2\Masters\Department;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;


class DepartmentController extends Controller
{
    //
    public function index(Request $request)
    {
        if ($request->isMethod('get')) {
            if ($request->ajax()) {
                $records = Department::query()
                    ->orderBy('id', 'DESC')
                    ->get()
                    ->toArray();
                return DataTables::of($records)
                    ->addColumn('action', function ($record) {
                        $action = '<div class="d-flex justify-content-center"><a href="/v2/master/departments/edit/' . $record['id'] . ' " class=" btn btn-success btn-sm " ><i class="fas fa-edit"></i> Edit</a>
                    <a href="/v2/master/departments/remove/' . $record['id'] . ' " class=" ml-2 btn btn-danger btn-sm remove"><i class="fa fa-trash"></i> Remove</a>';
                        return $action;
                    })
                    ->editColumn('status', function ($record) {
                        return $record['status'] ? 'Active' : 'Inactive';
                       
                    })
                    ->make(true);
            }
            return view('v2.masters.department.index');
        }
       else
        {
            $request->validate([
                'department' => 'required|regex:/^[a-zA-Z0-9 ]+$/|min:3|max:50',
            ]);
            $formValue = [
                'department' => $request->department,
                'status' => $request->status == '' ? 0 : 1
            ];
            Department::upsert($formValue, ['department_unique'], ['department', 'status']);
            return redirect('/v2/master/departments')->with("success", "Department has been inserted successfully!");
        }

    }

    // public function AddDepartments(Request $request)
    // {
    //     $request->validate([
    //         'department' => 'required',
    //     ]);
    //     $formValue = [
    //         'department' => $request->department,
    //         'status' => $request->status == '' ? 0 : 1
    //     ];
    //     Department::upsert($formValue, ['department_unique'], ['department', 'status']);
    //     return redirect('/v2/master/departments')->with("success", "Department has been inserted successfully!");
    // }

    public function EditDepartments($id)
    {
        $records = Department::find($id)->toArray();
        return view('v2.masters.department.index', compact('records'));
    }

    public function UpdateDepartments(Request $request, $id)
    {
        $request->validate([
            'department' => 'required|regex:/^[a-zA-Z0-9 ]+$/|min:3|max:50'
        ]);
        $records = [
            'department' => $request->department,
            'status' => $request->status == '' ? 0 : 1
        ];
        Department::where('id', $id)->update($records);
        return redirect('/v2/master/departments')->with("success", "Department has been updated successfully!");
    }


    public function DeleteDepartments($id)
    {
        Department::find($id)->delete();
        return redirect()->intended('/v2/master/departments')->with('danger', 'Department has been deleted successfully.');
    }
}
