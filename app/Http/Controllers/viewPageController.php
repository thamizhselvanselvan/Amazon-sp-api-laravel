<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SpApi;

class viewPageController extends Controller
{
    public function show()
    {
       
    }

    public function showInput(Request $request)
    {
        dd(str_replace([',','-','_'],[' '],$request->asinText));
        
        return view('showInput',['id'=>$request->asinText]);
    }

    public function spapitest()
    {
       
    }
}
