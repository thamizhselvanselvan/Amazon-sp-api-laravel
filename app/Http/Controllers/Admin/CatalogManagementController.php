<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class CatalogManagementController extends Controller
{
    function index(Request $request){

        if($request->ajax()){
            $users = User::whereHas(
                'roles', function($q){
                    $q->where('name', 'Catalog Manager ');
            })->latest()->orderBy('created_at');

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($user) {
                   
                    return "<a href='/admin/catalogpassword/".$user->id."' class='btn btn-primary btn-sm'><i class='fas fa-edit'></i>Change password</a>";
                })
                ->rawColumns(['action'])
                ->make(true);

        }
        return view('admin.catalogManagement.index');
    }

    function showResetPassword(Request $request){
        $user_id = $request->id;


     

        return view('admin.catalogManagement.password_reset', compact('user_id'));
    }
}
