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
    public function Racksadd()
    {
        return view('Inventory.Master.Racks.Add');
    }
    // public function save_racks(Request $request)
    // {

    //     $ra = Rack::create([
    //         'id' => $request->id,
    //         'name' => $request->name,

    //     ]);

    //     return redirect()->intended('/inventory/master/index')->with('success', 'Racks ' . $request->name . ' has been created successfully');
    // }
}
