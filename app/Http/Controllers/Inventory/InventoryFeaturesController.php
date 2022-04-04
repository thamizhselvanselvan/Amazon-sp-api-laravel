<?php

namespace App\Http\Controllers\Inventory;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InventoryFeaturesController extends Controller
{
    public function FeaturesIndex()
    {
        return view('Inventory.Features.Index');
    }
}