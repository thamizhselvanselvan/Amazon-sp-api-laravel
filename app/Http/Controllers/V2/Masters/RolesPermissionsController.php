<?php

namespace App\Http\Controllers\V2\Masters;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class RolesPermissionsController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::with('permissions')->get();
        if ($request->ajax()) {
            $roles = Role::with('permissions')->get();
            return DataTables::of($roles)
                ->addIndexColumn()
                ->editColumn('permissions', function ($roles) {
                    return $roles->permissions->first()['name'];
                })
                ->rawColumns(['permissions'])
                ->make(true);
        }
        return view('v2.masters.roles.index');
    }
}
