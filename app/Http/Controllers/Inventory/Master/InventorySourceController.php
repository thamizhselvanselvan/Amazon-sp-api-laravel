<?php

namespace App\Http\Controllers\Inventory\Master;

use Illuminate\Http\Request;
use App\Models\Inventory\Source;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class InventorySourceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        if ($request->ajax()) {

            $data = Source::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/sources/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('inventory.master.source.index');
    }

    public function create()
    {
        return view('inventory.master.source.add');
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|min:3|max:100',
        ]);

        Source::create([
            'name' => $request->name,
        ]);

        return redirect()->route('sources.index')->with('success', 'Source ' . $request->name . ' has been created successfully');
    }

    
    public function edit($id)
    {

        $name = Source::where('id', $id)->first();

        return view('inventory.master.source.edit', compact('name'));
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'name' => 'required|min:3|max:100',
        ]);

        Source::where('id', $id)->update($validated);

        return redirect()->route('sources.index')->with('success', 'Source has been updated successfully');
    }

    public function destroy($id)
    {
        Source::where('id', $id)->delete();

        return redirect()->route('sources.index')->with('success', 'Sources has been Deleted successfully');
    }
    
}
