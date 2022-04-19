<?php

namespace App\Http\Controllers\Inventory\Master;

use Illuminate\Http\Request;
use App\Models\Inventory\Rack;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

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
    public function save_racks(Request $request)
    {

        $sa = Rack::create([
            'name' => $request->name,

        ]);

        return redirect()->intended('/Inventory/Master/Racks/Index')->with('success', 'Racks ' . $request->name . ' has been created successfully');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Rack::query();

            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
        }

        return view('Inventory.Master.Racks.Index');
    }

    public function Shelvesview()
    {
        return view('Inventory.Master.Racks.Shelves.Index');
    }
    public function Shelvesadd()
    {
        return view('Inventory.Master.Racks.Shelves.add');
    }
}

