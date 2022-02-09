<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

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
