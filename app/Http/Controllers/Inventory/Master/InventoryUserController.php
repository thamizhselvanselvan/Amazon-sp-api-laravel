<?php

namespace App\Http\Controllers\Inventory\Master;

use App\Models\Roles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class InventoryUserController extends Controller
{
    public function UsersView()
    {
        return view('Inventory.Master.Users.Index');
                      
    }
    public function create()
    {
        //   $roles = Roles::get('name');
        return view('Inventory.Master.Users.Add');
                
    }

    // function password_Change_view(Request $request)
    // {
    //     $user_id = $request->id;
    //     return view('admin.adminManagement.password_reset', compact('user_id'));
    // }

    // public function password_reset_save(Request $request, $id)
    // {
    //     $request->validate([
    //         'password' => 'required|confirmed|min:3|max:18'
    //     ]);

    //     User::where('id', $id)->update([
    //         'password' => Hash::make($request->password)
    //     ]);

    //     return redirect()->intended('/Inventory/Master/Users/Index')->with('success', 'Admin password has been changed successfully');
    // }

   
    // public function save_user(Request $request)
    // {
    //     $request->validate([
    //         'password' => 'required|confirmed|min:3|max:18'
    //     ]);

    //     $im = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),

    //     ]);
    //     $role = $request->Role;
    //     $im->assignRole($role);

    //     return redirect()->intended('/Inventory/Master/Users/Index')->with('success', 'User ' . $request->name . ' has been created successfully');
    // }
 }