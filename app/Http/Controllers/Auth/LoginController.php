<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(){
        
        if(!Auth::check()){
            return view('auth.login');
        }

        $authRole = Auth::user()->roles->first()->name ?? "";

        if($authRole == 'Admin') {
            return redirect(Route('admin.dashboard'));
        }

        if($authRole == 'Seller') {
            return redirect(Route('dashboard'));
        }
        
        Auth::logout();
        return redirect(Route('login'));
    }

    public function redirectTo() {

        $authRole = Auth::user()->roles->first()->name;

        if($authRole == 'Admin') {
            return Route('admin.dashboard');
        }

        if($authRole == 'Seller') {
            return Route('dashboard');
        }

        return Route('home');
    }
}
