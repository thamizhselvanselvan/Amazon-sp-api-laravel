<?php

namespace App\Http\Controllers\Inventory\Master\Rack;

use Illuminate\Http\Request;
use App\Models\Inventory\Rack;
use App\Models\Inventory\Shelves;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class InventoryshelvesController extends Controller
{



    public function view()
    {
        return view('Inventory.Master.Racks.Shelves.Index');
    }

    public function add()
    {
        return view('Inventory.Master.Racks.Shelves.add');
    }

    public function save(Request $request)
    {


        $sa = Shelves::create([
            'Shelves_name' => $request->name,
            'No_of_Bins' => $request->bins,
        ]);

        return redirect()->intended('/Inventory/Master/Racks/Shelves/Index')->with('success', 'Shelves ' . $request->name . ' has been created successfully');
    }
    public function editshl($id)
    {

        $name = Rack::where('id', $id)->first();
        return view('Inventory.Master.Racks.Shelves.edit', compact('name'));
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Shelves::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="Edit_shl/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<a href="' . $row->id . '/user_delete" data-id=' . $row->id . ' class="trash btn btn-warning ml-2 btn-sm"><i class="far fa-trash-alt "></i> Remove</a>';
                    return $actionBtn;
                })
                ->make(true);
        }

        return view('Inventory/Master/Racks/Shelves/Index');
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([

             'name' => 'required|min:2|max:50',
             'No_of_Bins' => 'nullable|min:2|max:50'

        ]);
        Rack::where('id', $id)->update($validated);
        return redirect()->intended('/Inventory/Master/Racks/Shelves/Index')->with('success', 'Rack has been updated successfully');
    }





}
