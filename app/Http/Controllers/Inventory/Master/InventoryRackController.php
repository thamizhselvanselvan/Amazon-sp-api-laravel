<?php

namespace App\Http\Controllers\Inventory\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryRackController extends Controller
{
    public function RacksView()
    {
        return view('Inventory.Master.Racks.Index');
                      
    }
}
