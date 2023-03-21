<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Roles;
use Illuminate\Http\Request;
use App\Models\Aws_credential;
use App\Models\Mws_region;
use App\Models\Admin\BB\BB_User;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Company\CompanyMaster;
use Yajra\DataTables\Facades\DataTables;
use App\Models\order\OrderSellerCredentials;
use App\Models\V2\Masters\Credential;

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

            $store_cred_array = [];
            $store_status_array = [];
            $store_order_item = [];
            $shipntrack = [];
            $zoho = [];
            $bb_store = [];
            $source_check = [];
            $destination_check = [];
            $courier_partner_check = [];

            $source_destination = [
                'IN' => 'IND',
                'US' => 'USA',
                'AE' => 'UAE',
                'SA' => 'KSA'
            ];
            $courier_partner = [
                'B2CShip' => 'B2CShip'
            ];
            $bb_store_status =  OrderSellerCredentials::where('buybox_stores', 1)->get();
            foreach ($bb_store_status as $data) {
                $seller = $data['seller_id'];
                if ($data['buybox_stores']) {
                    $bb_store[$seller] = 1;
                }
            }

            $store_status = OrderSellerCredentials::where('dump_order', 1)->get();
            foreach ($store_status as $key => $value) {

                $seller = $value['seller_id'];
                $store_status_array[$seller] = 1;

                if ($value['cred_status'] == 0) {
                    $store_cred_array[$seller] = 1;
                }
                if ($value['get_order_item'] == 1) {
                    $store_order_item[$seller] = 1;
                }
                if ($value['enable_shipntrack']) {
                    $shipntrack[$seller] = 1;
                }
                if ($value['zoho']) {
                    $zoho[$seller] = 1;
                }

                if ($value['source']) {
                    $source_check[$seller] = $value['source'];
                }
                if ($value['destination']) {
                    $destination_check[$seller] = $value['destination'];
                }
                if ($value['courier_partner']) {
                    $courier_partner_check[$seller] = $value['courier_partner'];
                }
            }

            $aws_credential = Aws_Credential::with('mws_region')->where('api_type', 1)->get();
            return DataTables::of($aws_credential)
                ->addIndexColumn()

                ->editColumn('store_name', function ($id) use ($store_cred_array, $store_status_array) {
                    if (array_key_exists($id['seller_id'], $store_cred_array) && array_key_exists($id['seller_id'], $store_status_array)) {

                        $action =  $id['store_name'] . ' <span style="font-size: 14px; background-color:#ff0000ba; border-radius:30px; color:white; padding:0px 5px;"> Inactive </span>';
                        return $action;
                    } elseif (array_key_exists($id['seller_id'], $store_status_array)) {

                        $action =  $id['store_name'] . ' <span style="font-size: 14px; background-color: #41a20f; border-radius:30px; color:white; padding:0px 5px;"> Active </span>';
                        return $action;
                    }
                    return $id['store_name'];
                })
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

                ->addColumn('buybox_stores', function ($row) use ($bb_store) {
                    if (array_key_exists($row['seller_id'], $bb_store)) {
                        $action = '<div class="pl-2">
                            <input class="bb_store" type="checkbox" checked value=' . $row['seller_id'] . ' id="bb_store' . $row['seller_id'] . '" name="bb_store[]">
                        </div>';
                    } else {
                        $action = '<div class="pl-2">
                            <input class="bb_store" type="checkbox"  value=' . $row['seller_id'] . ' id="bb_store' . $row['seller_id'] . '" name="bb_store[]">
                        </div>';
                    }
                    return $action;
                })

                ->addColumn('partner', function ($id) use ($courier_partner, $courier_partner_check) {
                    $action = '<div class="pl-2">
                                    <select name="courier[]" id="courier" class="courier_class">
                                        <option value="NULL">Select Courier</option>';
                    foreach ($courier_partner as $key => $value) {

                        if (array_key_exists($id['seller_id'], $courier_partner_check) && $courier_partner_check[$id['seller_id']] == $key) {
                            $action .= '<option value="' . $key . ':' . $id['id'] . ' "selected>' . $value . '</option>';
                        } else {
                            $action .= '<option value="' . $key . ':' . $id['id'] . '">' . $value . '</option>';
                        }
                    }
                    return $action .= '</select></div>';
                })
                ->addColumn('source', function ($id) use ($source_destination, $source_check) {
                    $action = '';
                    $action .= '<div class="pl-2">
                                    <select name="source[]" class="source">
                                        <option value="NULL">Select Source</option>';
                    foreach ($source_destination as $key => $value) {
                        if (array_key_exists($id['seller_id'], $source_check) && $source_check[$id['seller_id']] == $key) {
                            $action .= '<option value="' . $key . ':' . $id['id'] . ' "selected>' . $value . '</option>';
                        } else {
                            $action .= '<option value="' . $key . ':' . $id['id'] . '">' . $value . '</option>';
                        }
                    }
                    return $action .= '</select></div>';
                })
                ->addColumn('destination', function ($id) use ($source_destination, $destination_check) {
                    $action = '<div class="pl-2">
                                <select name="destination[]" class="destination">
                                    <option value="NULL">Select Destination</option>';
                    foreach ($source_destination as $key => $value) {
                        if (array_key_exists($id['seller_id'], $destination_check) && $destination_check[$id['seller_id']] == $key) {
                            $action .= '<option value="' . $key . ':' . $id['id'] . ' "selected>' . $value . '</option>';
                        } else {
                            $action .= '<option value="' . $key . ':' . $id['id'] . '">' . $value . '</option>';
                        }
                    }
                    return $action .= '</select></div>';
                })

                ->addColumn('push_price_type', function ($id) {
                    $action = '<div class="d-flex justify-content-center "><a id="update-push-price" href="javascript:void(0)" class=" btn btn-success btn-sm " data-toggle="modal" data-id=' . $id['id'] . '><i class="fas fa-edit"></i> Edit</a></div>';

                    // $action = '<a href="/admin/stores/update/' . $id['id'] . ' " class=" btn btn-success btn-sm " ><i class="fas fa-edit"></i> Edit</a></div>';
                    return $action;
                })
                ->rawColumns(['store_name', 'region', 'order', 'order_item', 'enable_snt', 'partner', 'zoho', 'buybox_stores', 'source', 'destination', 'push_price_type'])
                ->make(true);
        }

        return view('orders.listorders.selectstore');
    }

    public function updateStore(Request $request)
    {

        $order_items = explode('-', $request->order_item);
        $selected_store = explode('-', $request->selected_store);
        $shipntrack = explode('-', $request->shipntrack);
        $zoho_enables = explode('-', $request->zoho_enable);
        $bb_store_enables = explode('-', $request->bb_store_enable);
        $courier_partners = explode('-', $request->courier_partner);
        $source = explode('-', $request->source);
        $destination = explode('-', $request->destination);


        $shipntrack_array = [];
        $zoho_enable_array = [];
        $bb_store_enable_array = [];
        $courier_partner_arr = [];
        $source_arr = [];
        $des_arr = [];

        foreach ($order_items as $key => $value) {
            $order_item[$value] = 1;
        }

        foreach ($shipntrack as $key => $shipntrack_value) {
            $shipntrack_array[$shipntrack_value] = 1;
        }

        foreach ($zoho_enables as $zoho_enable) {
            $zoho_enable_array[$zoho_enable] = 1;
        }
        foreach ($bb_store_enables as $bb_store_enable) {
            $bb_store_enable_array[$bb_store_enable] = $bb_store_enable;
        }

        if (isset($request->courier_partner)) {

            foreach ($courier_partners as $courier_partner) {
                $courier_partner_tem = explode(':', $courier_partner);
                $courier_partner_arr[trim($courier_partner_tem[1])] = trim($courier_partner_tem[0]);
            }
        }

        if ($request->source && $request->destination) {
            foreach ($source as $src) {
                $src_tem = explode(':', $src);
                $source_arr[trim($src_tem[1])] = trim($src_tem[0]);
            }
            foreach ($destination as $des) {
                $des_tem = explode(':', $des);
                $des_arr[trim($des_tem[1])] = trim($des_tem[0]);
            }
        }

        OrderSellerCredentials::query()->update([
            'dump_order' => 0,
            'get_order_item' => 0,
            'enable_shipntrack' => 0,
            'zoho' => 0,
            'buybox_stores' => 0,
            'courier_partner' => NULL,
            'source' => NULL,
            'destination' => NULL,
        ]);

        if ($selected_store['0'] != '') {
            foreach ($selected_store as $id) {

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

                if (array_key_exists($id, $courier_partner_arr)) {

                    $aws_cred_array['courier_partner'] = $courier_partner_arr[$id];
                }

                if (array_key_exists($id, $source_arr)) {

                    $aws_cred_array['source'] = $source_arr[$id];
                }
                if (array_key_exists($id, $des_arr)) {
                    $aws_cred_array['destination'] =  $des_arr[$id];
                }

                OrderSellerCredentials::upsert([$aws_cred_array], ['seller_id'], [
                    'seller_id',
                    'store_name',
                    'country_code',
                    'dump_order',
                    'get_order_item',
                    'enable_shipntrack',
                    'courier_partner',
                    'zoho',
                    'buybox_stores',
                    'source',
                    'destination'
                ]);
            }
        }

        if (($bb_store_enables['0'] != '')) {

            foreach ($bb_store_enable_array as $id) {
                $aws_cred = Aws_credential::with(['mws_region'])->where('seller_id', $id)->get();
                if ($aws_cred) {
                    $aws_cred_array = [
                        'seller_id' => $aws_cred[0]->seller_id,
                        'country_code' => $aws_cred[0]['mws_region']->region_code,
                        'store_name' => $aws_cred[0]->store_name,
                        'buybox_stores' => 1
                    ];
                    OrderSellerCredentials::upsert($aws_cred_array, ['seller_id'], ['buybox_stores']);
                }
            }
        }

        return response()->json(['success' => 'Store Selected']);
    }

    public function UpdatePushPriceColumn(Request $request)
    {
        $id = $request->updated_id;
        $request->validate([
            'type' => 'required|in:fixed,percentage',
            'value' => 'required'
        ]);
        $Updated_data = [
            'price_calculation_type' => $request->type,
            'price_calculation_value' => $request->value
        ];

        OrderSellerCredentials::where('id', $id)->update($Updated_data);
        return redirect()->intended('/admin/stores')->with('success', 'Records has been updated successfully');
    }

    public function EditPushPriceColumn($id)
    {
        $records = OrderSellerCredentials::select('price_calculation_type', 'price_calculation_value')->where('id', $id)->get();
        return response()->json($records);
    }

    public function credentialmanage(Request $request, $id = null)
    {
        $data_mws =  Mws_region::query()->select('id', 'region', 'region_code')->distinct()->get();
        $request_Region = $id;

        $url = "/admin/creds/manage";
        if (isset($request_Region)) {
            $url = "/admin/creds/manage/" . $request_Region;
        }

        if ($request->ajax()) {
            $data = Aws_credential::query()
                ->select('id', 'store_name', 'merchant_id', 'credential_use', 'mws_region_id', 'country_priority', 'horizon_priority', 'credential_priority')
                ->when($request->region, function ($query, $id) use ($request) {
                    return $query->where('mws_region_id', $id);
                })
                ->get();

            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    $val = $row->mws_region_id . '_' . $row->id;
                    $actionBtn = "<div class='d-flex'><a href='javascript:void(0)' data-toggle='modal' data-target='.bd-example-modal-sm' value='$val' id='credentials' class='creds btn btn-success btn-sm'><i class='fas fa-save'></i> Update</a>";
                    return $actionBtn;
                })
                ->addColumn('Creds_priority', function ($row) {
                    $value = $row->credential_priority;
                    if ($value == '1') {
                        $data = 'P1';
                    } else if ($value == '2') {
                        $data = 'P2';
                    } else if ($value == '3') {
                        $data = 'P3';
                    } else if ($value == '4') {
                        $data = 'P4';
                    } else if ($value == null) {
                        $data = '';
                    } else {
                        $data = 'unknown ';
                    }
                    return $data;
                })
                ->rawColumns(['action', 'Creds_priority'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('admin.adminManagement.creds_manage', compact('data_mws', 'request_Region', 'url'));
    }

    public function credentialprioritysave(Request $request)
    {

        $store_id = $request->sell_id;
        $priority = $request->priority;
       
        $data = Aws_credential::with(['mws_region'])->where('id', $store_id)->get()->toArray();

        $region_code = $data['0']['mws_region']['region_code'];
        
        Aws_credential::where('id', $store_id)->update(['country_priority' => $region_code, 'credential_priority' => $priority]);

        return redirect()->intended('/admin/creds/manage')->with('success', 'Country Priority has been updated successfully');
    }
    public function horizonprioritysave(Request $request)
    {
      
        $store_id = $request->sell_id;
        $priority = $request->priority;
        Aws_credential::where('id', $store_id)->update(['horizon_priority' => $priority]);

        return redirect()->intended('/admin/creds/manage')->with('success', 'Horizon Priority has been updated successfully');
    }
}
