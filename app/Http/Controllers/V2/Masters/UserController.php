<?php

namespace App\Http\Controllers\V2\Masters;


use App\Models\V2\Masters\BB\BB_User;
use App\Models\V2\Masters\CompanyMaster;
use App\Models\V2\Masters\User;
use App\Models\V2\Masters\Roles;
use Illuminate\Http\Request;
use App\Models\Aws_credential;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use App\Models\order\OrderSellerCredentials;//Need to change


class UserController extends Controller
{
    function index(Request $request)
    {

        $user = Auth::user();
        $login_id = $user->id;
        $role = $user->roles->first()->name;
        $users = User::latest()->where('id', '>', '1')->orderBy('id', 'DESC')->get();
        if ($request->ajax()) {

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($user)  use ($login_id, $role) {
                    $edit = '';
                    if ($login_id == $user->id || $role == 'Admin' && $user->id != 1) {
                        $edit = "<a href='/v2/master/users/password_reset/" . $user->id . "' class='btn btn-primary btn-sm mr-2'><i class='fas fa-edit'></i>Change password</a>";
                    }
                    if ($login_id == $user->id || $role == 'Admin' && $user->id != 1) {
                        $edit .= '<a href="/v2/master/users/' . $user->id . '/edit" class="edit btn btn-success btn-sm"> <i class="fas fa-edit"></i> Edit</a>';
                    }
                    if ($login_id == $user->id || $role == 'Admin' && $user->id != 1) {
                        $edit .= '<button  remove-btn="' . $user->id . '" class="ml-2 btn btn-danger btn-sm" id="remove">
                        <i class="fa fa-remove"></i> Remove</button>';
                    }
                    return $edit;
                })
                ->addColumn('permission', function ($permission) {
                    $roles = $permission->roles;
                    $roles = json_decode($roles);
                    $multiple_roles = '';
                    foreach ($roles as $key => $role) {
                        $multiple_roles .= $role->name . ', ';
                    }

                    return rtrim($multiple_roles, ', ');
                })
                ->rawColumns(['action', 'permission'])
                ->make(true);
        }
        return view('v2.masters.users.index');
    }
}
