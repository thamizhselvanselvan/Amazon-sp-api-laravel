<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class AdminManagementController extends Controller
{
    function index(Request $request){

        if($request->ajax()){
            $users = User::whereHas(
                'roles', function($q){
                    $q->where('name', 'Admin');
            })->latest()->orderBy('created_at');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($user) {
                   
                    return "<a href='/admin/adminpassword/".$user->id."' class='btn btn-primary btn-sm'><i class='fas fa-edit'></i>Change password</a>";
                })
                ->rawColumns(['action'])
                ->make(true);

        }
        return view('admin.adminManagement.index');
    }
}
