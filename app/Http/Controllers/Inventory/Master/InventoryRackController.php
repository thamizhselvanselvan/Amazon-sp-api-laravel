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
            'number of Shelves' => $request->shelves,
        ]);

        return redirect()->intended('/Inventory/Master/Racks/Index')->with('success', 'Racks ' . $request->name . ' has been created successfully');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Rack::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="Edit_rack/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<a href="' . $row->id . '/user_delete" data-id=' . $row->id . ' class="trash btn btn-warning ml-2 btn-sm"><i class="far fa-trash-alt "></i> Remove</a>';
                    return $actionBtn;
                })
                ->make(true);
        }

        return view('Inventory.Master.Racks.Index');
    }

    public function editRack($id)
    {

        $name = Rack::where('id', $id)->first();
        return view('Inventory.Master.Racks.edit', compact('name'));
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([

             'name' => 'required|min:2|max:50',

        ]);
        Rack::where('id', $id)->update($validated);
        return redirect()->intended('/Inventory/Master/Racks/Index')->with('success', 'Rack has been updated successfully');
    }

    public function Shelvesview()
    {
        return view('Inventory.Master.Racks.Shelves.Index');
    }
    public function Shelvesadd()
    {
        return view('Inventory.Master.Racks.Shelves.add');
    }

    public function binview()
    {
        return view('Inventory.Master.Racks.Bin.Index');
    }
    public function binadd()
    {
        return view('Inventory.Master.Racks.Bin.add');
    }
}
