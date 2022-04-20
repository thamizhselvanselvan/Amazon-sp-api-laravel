<?php

namespace App\Http\Controllers\Inventory\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventoryDestinationController extends Controller
{
    public function destinationview()
    {
        return view('Inventory.Master.Destination.Index');
    }
    public function destinationadd()
    {
        return view('Inventory.Master.Destination.add');
    }
}
