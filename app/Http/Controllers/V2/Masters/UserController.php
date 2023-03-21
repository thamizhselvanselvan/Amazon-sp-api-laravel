<?php

namespace App\Http\Controllers\V2\Masters;

use Illuminate\Http\Request;
use App\Models\V2\Masters\User;
use App\Models\V2\Masters\Roles;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\V2\Masters\Department;
use App\Models\V2\Masters\CompanyMaster;
use Yajra\DataTables\Facades\DataTables;


class UserController extends Controller
{
    function index(Request $request)
    {

        $user = Auth::user();
        $login_id = $user->id;
        $role = $user->roles->first()->name;
        $users = User::with('companys')->latest()->where('id', '>', '1')->orderBy('id', 'DESC')->get();
        if ($request->isMethod('get')) {
            if ($request->ajax()) {

                return DataTables::of($users)
                    ->addIndexColumn()
                    ->addColumn('action', function ($user) use ($login_id, $role) {
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
                    ->editColumn('company', function ($data) {
                        $user_companys=$data->companys->pluck('company_name')->toArray();
                        return implode(',',$user_companys);
                    })
                    ->rawColumns(['action', 'permission'])
                    ->make(true);
            }
            return view('v2.masters.users.index');
        } else {

            $request->validate([
                'name' => 'required|regex:/^[\pL\s\-]+$/u|min:3|max:150',
                'email' => 'required|email|unique:App\Models\V2\Masters\User|max:150',
                'password' => 'required|confirmed|min:6|max:20',
                'company' => 'required'

            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'department_id' => $request->department,

            ]);

            $user->companys()->attach($request->company);//insert data to pivot table
            $role = $request->role;
            $user->assignRole($role);
            return redirect()->intended('/v2/master/users')->with('success', 'User ' . $request->name . ' has been created successfully');
        }
    }
    public function create()
    {
        $roles = Roles::get('name');
        $companys = CompanyMaster::where('user_id', Auth::id())->get();
        $departments = Department::where('status', 1)->get();
        return view('v2.masters.users.add', compact(['roles', 'companys', 'departments']));
    }

    function password_reset(Request $request, $id)
    {

        $user = User::where('id', $id)->exists();

        if (!$user) {
            return redirect()->intended('/v2/master/users')->with("error", "User does not exists");
        }

        $user = Auth::user();
        $login_id = $user->id;
        $role = $user->roles->first()->name;

        $user_id = $request->id;

        if ($login_id == $user_id || $role == 'Admin' && $user_id != 1) {
            return view('v2.masters.users.password_reset', compact('user_id'));
        }

        return redirect()->intended('/v2/master/users')->with("error", "You don't have permission to change the password");
    }

    public function password_reset_save(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6|max:20'
        ]);

        $user = User::where('id', $id)->exists();

        if (!$user) {
            return redirect()->intended('/v2/master/users')->with("error", "User does not exists");
        }

        User::where('id', $id)->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->intended('/v2/master/users')->with('success', 'Password has been changed successfully');
    }

    public function edit(Request $request)
    {
        $users = User::find($request->id);
        $selected_roles = $users->roles->pluck('name')->toArray();
        $roles = Roles::get('name');
        $companys = CompanyMaster::where('user_id', Auth::id())->get();
        $departments = Department::where('status', 1)->get();
        $userCompanys = $users->companys()->allRelatedIds()->toArray();
        return view('v2.masters.users.edit', compact(['roles', 'companys', 'departments', 'selected_roles', 'users','userCompanys']));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|regex:/^[\pL\s\-]+$/u|min:3|max:150',
            'email' => 'required|email|max:150',
            'company' => 'required'
        ]);
        $user = User::find($request->id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department,
        ]);
        $user->companys()->sync($request->company); //requested companies  will be kept and others will  be removed from pivot table
        $role = $request->role;
        $user->roles()->detach();
        $user->assignRole($role);
        return redirect()->intended('/v2/master/users')->with('success', 'User ' . $request->name . ' has been updated successfully');
    }

    public function delete($id)
    {
        User::find($id)->companys()->detach();
        User::find($id)->delete();
        return response()->json(['success' => 'User has been deleted successfully']);
    }
}
