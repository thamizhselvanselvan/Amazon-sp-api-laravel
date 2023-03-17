<?php

namespace App\Http\Controllers\shipntrack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingMasterController extends Controller
{
    public function index()
    {
         return view('shipntrack.Booking.index');
    }
}
