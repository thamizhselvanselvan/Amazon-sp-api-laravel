<?php

namespace App\Http\Controllers\AmazonInvoice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AmazonInvoiceManagementController extends Controller
{

    public function index()
    {
        return view('amazonInvoice.index');
    }

    public function uploadInvoice()
    {
        
    }
}
