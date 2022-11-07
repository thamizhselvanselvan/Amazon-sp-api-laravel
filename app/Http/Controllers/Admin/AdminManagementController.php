<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Roles;
use Illuminate\Http\Request;
use App\Models\Admin\BB\BB_User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Company\CompanyMaster;
use Yajra\DataTables\Facades\DataTables;
use App\Models\order\OrderSellerCredentials;
use App\Models\Aws_credential;

class AdminManagementController extends Controller
{
    function index(Request $request)
    {

        $user = Auth::user();
        $login_id = $user->id;
        $role = $user->roles->first()->name;
        $users = User::latest()->where('id', '>', '1')->orderBy('id', 'DESC')->get();
        // dd($users[0]->roles);
        if ($request->ajax()) {

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($user)  use ($login_id, $role) {
                    $edit = '';
                    if ($login_id == $user->id || $role == 'Admin' && $user->id != 1) {
                        $edit = "<a href='password_reset_view/" . $user->id . "' class='btn btn-primary btn-sm mr-2'><i class='fas fa-edit'></i>Change password</a>";
                    }
                    if ($login_id == $user->id || $role == 'Admin' && $user->id != 1) {
                        $edit .= '<a href="/admin/' . $user->id . '/edit" class="edit btn btn-success btn-sm"> <i class="fas fa-edit"></i> Edit</a>';
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
        return view('admin.adminManagement.index');
    }

    function password_Change_view(Request $request, $id)
    {

        $user_exists = User::where('id', $id)->exists();

        if (!$user_exists) {
            return redirect()->intended('/admin/user_list')->with("error", "User does not exists");
        }

        $user = Auth::user();
        $login_id = $user->id;
        $role = $user->roles->first()->name;

        $user_id = $request->id;

        if ($login_id == $user_id || $role == 'Admin' && $user_id != 1) {
            return view('admin.adminManagement.password_reset', compact('user_id'));
        }

        return redirect()->intended('/admin/user_list')->with("error", "You don't have permission to change the password");
    }

    public function password_reset_save(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|confirmed|min:3|max:18'
        ]);

        $user_exists = User::where('id', $id)->exists();

        if (!$user_exists) {
            return redirect()->intended('/admin/user_list')->with("error", "User does not exists");
        }

        User::where('id', $id)->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->intended('/admin/user_list')->with('success', 'Admin password has been changed successfully');
    }

    public function create()
    {
        $roles = Roles::get('name');
        $companys = CompanyMaster::get();
        $bb_user = BB_User::get(['name', 'id']);
        return view('admin.adminManagement.add', compact(['roles', 'companys', 'bb_user']));
    }

    public function save_user(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:3|max:18'
        ]);
        // return $request->all();
        $seller_id = NULL;
        foreach ($request->Role as $role) {
            if ($role == 'Seller') {
                $seller_id = $request->bb_user;
            }
        }

        $am = User::create([
            'name' => $request->name,
            'bb_seller_id' => $seller_id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company,

        ]);
        $role = $request->Role;
        $am->assignRole($role);

        return redirect()->intended('/admin/user_list')->with('success', 'User ' . $request->name . ' has been created successfully');
    }

    public function edit(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        $user_id = $request->id;

        $user_email = $user->email;
        $user_name = $user->name;
        $selected_roles = $user->roles->first()->name;
        $selected_company = $user->company_id;

        $roles = Roles::get('name');
        $companys = CompanyMaster::get();
        return view('admin.adminManagement.edit', compact(['roles', 'companys', 'user_name', 'user_email', 'selected_roles', 'selected_company', 'user_id']));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required'
        ]);
        $user = User::where('id', $id)->first();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'company_id' => $request->company,
        ]);

        $role = $request->Role;
        $user->roles()->detach();
        $user->assignRole($role);
        return redirect()->intended('/admin/user_list')->with('success', 'User ' . $request->name . ' has been updated successfully');
        // return $request;
    }

    public function delete($id)
    {
        $user = User::find($id)->delete();
        // return redirect()->intended('/admin/user_list')->with('success', 'User  has been deleted successfully');
        return response()->json(['success' => 'User has been deleted successfully']);
    }

    public function bin(Request $request)
    {
        $user = Auth::user();
        $login_id = $user->id;
        $role = $user->roles->first()->name;
        $users = User::onlyTrashed()->get();
        // po($users);
        if ($request->ajax()) {
            return dataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function ($user) use ($login_id) {
                    $action = '';
                    $action = "<a href='role-restore/" . $user->id . "' class='btn btn-success btn-sm mr-2'><i class='fas fa-trash-restore'></i>Restore</a>";
                    return $action;
                })
                ->addColumn('role', function ($role) {
                    $roles = $role->roles;
                    $roles = json_decode($roles);
                    $multiple_roles = '';
                    foreach ($roles as $key => $role) {
                        $multiple_roles .= $role->name;
                    }
                    return ($multiple_roles);
                })
                ->rawColumns(['action', 'role'])
                ->make(true);
        }
        return view('admin.adminManagement.bin');
    }

    public function restore($id)
    {
        $restore = User::withTrashed()->find($id)->restore();
        return redirect()->intended('/admin/user_list')->with('success', 'User  has been restored successfully');
    }

    public function selectStore(Request $request)
    {
        if ($request->ajax()) {

            $store_status_array = [];
            $store_order_item = [];
            $shipntrack = [];
            $zoho = [];
            $store_status = OrderSellerCredentials::where('dump_order', 1)->get();
            foreach ($store_status as $key => $value) {
                $seller = $value['seller_id'];
                $store_status_array[$seller] = 1;

                if ($value['get_order_item'] == 1) {

                    $store_order_item[$seller] = 1;
                }
                if ($value['enable_shipntrack']) {

                    $shipntrack[$seller] = 1;
                }
                if ($value['zoho']) {
                    $zoho[$seller] = 1;
                }
            }
            $aws_credential = Aws_Credential::with('mws_region')->where('api_type', 1)->get();
            return DataTables::of($aws_credential)
                ->addIndexColumn()
                ->editColumn('region', function ($mws_region) {

                    return $mws_region['mws_region']['region'] . ' [' . $mws_region['mws_region']['region_code'] . ']';
                })
                ->addColumn('order', function ($id) use ($store_status_array) {
                    if (array_key_exists($id['seller_id'], $store_status_array)) {
                        $action = '<div class="pl-2">
                            <input class="order" type="checkbox" checked value=' . $id['id'] . ' id="order' . $id['id'] . '"  name="options[]" >
                        </div>';
                    } else {
                        $action = '<div class="pl-2">
                            <input class="order" type="checkbox" value=' . $id['id'] . ' id="order' . $id['id'] . '" name="options[]" >
                        </div>';
                    }
                    return $action;
                })
                ->addColumn('order_item', function ($id) use ($store_order_item) {
                    if (array_key_exists($id['seller_id'], $store_order_item)) {
                        $action = '<div class="pl-2">
                            <input class="order_item" type="checkbox" checked value=' . $id['id'] . ' id="orderitem' . $id['id'] . '" name="orderItem[]" >
                        </div>';
                    } else {
                        $action = '<div class="pl-2"><input class="order_item" type="checkbox" disabled value=' . $id['id'] . ' id="orderitem' . $id['id'] . '" name="orderItem[]" ></div>';
                    }
                    return $action;
                })
                ->addColumn('enable_snt', function ($id) use ($shipntrack) {
                    if (array_key_exists($id['seller_id'], $shipntrack)) {
                        $action = '<div class="pl-2">
                            <input class="shipntrack" type="checkbox" checked value=' . $id['id'] . ' id="shipntrack' . $id['id'] . '" name="shipntrack[]">
                        </div>';
                    } else {
                        $action = '<div class="pl-2">
                                    <input class="shipntrack" type="checkbox" disabled value=' . $id['id'] . ' id="shipntrack' . $id['id'] . '" name="shipntrack[]" >
                                </div>';
                    }
                    return $action;
                })
                ->addColumn('zoho', function ($id) use ($zoho) {
                    if (array_key_exists($id['seller_id'], $zoho)) {

                        $action = '<div class="pl-2">
                            <input class="zoho" type="checkbox" checked value=' . $id['id'] . ' id="zoho' . $id['id'] . '" name="zoho[]">
                        </div>';
                    } else {
                        $action = '<div class="pl-2">
                            <input class="zoho" type="checkbox" disabled value=' . $id['id'] . ' id="zoho' . $id['id'] . '" name="zoho[]">
                        </div>';
                    }
                    return $action;
                })
                ->addColumn('partner', function ($id) {
                    $action = '<div class="pl-2">
                                    <select name="courier[]" id="courier" class="courier_class">
                                        <option value="NULL">Select Courier</option>
                                        <option value="B2CShip' . $id['id'] . '">B2CShip</option>
                                    </select>
                                </div>';
                    return $action;
                })
                ->addColumn('source', function () {
                    $action = '<div class="pl-2">
                                    <select name="source[]" class="source">
                                        <option value="NULL">Select Source</option>
                                        <option value="IND">IND</option>
                                        <option value="USA">USA</option>
                                        <option value="UAE">UAE</option>
                                        <option value="KSA">KSA</option>
                                    </select>
                                </div>';

                    return $action;
                })
                ->addColumn('destination', function () {
                    $action = '<div class="pl-2">
                                <select name="destination[]" class="destination">
                                    <option value="NULL">Select Destination</option>
                                    <option value="IND">IND</option>
                                    <option value="USA">USA</option>
                                    <option value="UAE">UAE</option>
                                    <option value="KSA">KSA</option>
                                </select>
                             </div>';

                    return $action;
                })
                ->rawColumns(['region', 'order', 'order_item', 'enable_snt', 'partner', 'zoho', 'source', 'destination'])
                ->make(true);
        }

        return view('orders.listorders.selectstore');
    }

    public function updateStore(Request $request)
    {
        // return $request->all();
        $order_items = explode('-', $request->order_item);
        $selected_store = explode('-', $request->selected_store);
        $shipntrack = explode('-', $request->shipntrack);
        $zoho_enables = explode('-', $request->zoho_enable);
        po($zoho_enables);

        $shipntrack_array = [];
        $zoho_enable_array = [];

        foreach ($order_items as $key => $value) {
            $order_item[$value] = 1;
        }

        foreach ($shipntrack as $key => $shipntrack_value) {
            $shipntrack_array[$shipntrack_value] = 1;
        }

        foreach ($zoho_enables as $zoho_enable) {
            $zoho_enable_array[$zoho_enable] = 1;
        }

        // po($zoho_enable_array);
        // exit;
        OrderSellerCredentials::query()->update([
            'dump_order' => 0,
            'get_order_item' => 0,
            'enable_shipntrack' => 0
        ]);

        foreach ($selected_store as $key => $id) {

            $aws_cred = Aws_credential::with(['mws_region'])->where('id', $id)->get();
            $aws_cred_array = [
                'seller_id' => $aws_cred[0]->seller_id,
                'country_code' => $aws_cred[0]['mws_region']->region_code,
                'store_name' => $aws_cred[0]->store_name,
                'dump_order' => 1
            ];

            if (array_key_exists($id, $order_item)) {
                $aws_cred_array['get_order_item'] = 1;
            }
            if (array_key_exists($id, $shipntrack_array)) {
                $aws_cred_array['enable_shipntrack'] = 1;
            }
            if (array_key_exists($id, $zoho_enable_array)) {
                $aws_cred_array['zoho'] = 1;
            }
            // return $aws_cred_array;
            OrderSellerCredentials::upsert([$aws_cred_array], ['seller_id'], [
                'seller_id',
                'store_name',
                'country_code',
                'dump_order',
                'get_order_item',
                'enable_shipntrack',
                'zoho',
            ]);
        }

        return response()->json(['success' => 'Store Selected']);
    }
}
