<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function dashboard(Request $request)
    {
        $da = '';
        $user = Auth::user();
        if (isset($user->name)) {
            Alert::success(
                "Welcome  $user->name",
                'To App 360'
            );
        }
        return view('admin.dashboard', compact(('da')));
    }
    public function index()
    {
        return view('home');
    }
}
