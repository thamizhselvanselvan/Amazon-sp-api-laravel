<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PhpParser\Node\Expr\FuncCall;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class RolesPermissionsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $roles = Role::with('permissions')->get();

            return DataTables::of($roles)
                ->addIndexColumn()
                ->editColumn('permissions', function ($roles) {

                    return $roles->permissions[0]['name'];
                })
                
                ->make(true);
        }
        return view('admin.roles.index');
    }

    public function show(Request $request)
    {
        if($request->ajax()){
            $users = User::whereHas(
                'roles', function($q){
                    $q->where('name', 'Admin');
            })->latest()->orderBy('created_at');

            return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('action', function ($user) {
               
                return "<a href='/adminpassword/".$user->id."' class='btn btn-primary btn-sm'>Change password</a>";
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('admin.role.show');
    }
}
