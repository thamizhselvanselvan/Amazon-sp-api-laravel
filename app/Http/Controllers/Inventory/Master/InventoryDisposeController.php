<?php

namespace App\Http\Controllers\Inventory\Master;

use Illuminate\Http\Request;
use App\Models\Inventory\Dispose;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class InventoryDisposeController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Dispose::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/disposes/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('inventory.master.dispose.index');
    }

    public function create()
    {
        return view('inventory.master.dispose.add');
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'reason' => 'required|min:3|max:1000',
        ]);

        Dispose::create([
            'reason' => $request->reason,
        ]);

        return redirect()->route('disposes.index')->with('success', 'Dispose Reason has been created successfully');
    }

    
    public function edit($id)
    {

        $name = Dispose::where('id', $id)->first();

        return view('inventory.master.dispose.edit', compact('name'));
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'reason' => 'required|min:3|max:1000',
        ]);

        Dispose::where('id', $id)->update($validated);

        return redirect()->route('disposes.index')->with('success', 'Dispose Reason has been updated successfully');
    }

    public function destroy($id)
    {
        Dispose::where('id', $id)->delete();

        return redirect()->route('disposes.index')->with('success', 'Dispose has been Deleted successfully');
    }
}
