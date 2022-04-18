<?php

namespace App\Http\Controllers\Inventory\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InventorySourceController extends Controller
{
    public function sourceview()
    {
        return view('Inventory.Master.Source.Index');
    }
    public function sourceadd()
    {
        return view('Inventory.Master.Source.add');
    }
}
