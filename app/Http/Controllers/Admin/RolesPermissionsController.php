<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;

class RolesPermissionsController extends Controller
{
    public function index()
    {
        return view('admin.roles.show');
    }
}
