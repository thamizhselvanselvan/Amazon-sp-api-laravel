<?php

namespace App\Http\Controllers\Inventory\Master;

use Illuminate\Http\Request;
use App\Models\Inventory\Tag;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class TagController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Tag::query();

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/tags/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('inventory.master.tag.index');
    }
    public function create()
    {
        return view('inventory.master.tag.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:100',
        ]);

        Tag::create([
            'name' => $request->name,
        ]);

        return redirect()->route('tags.index')->with('success', 'Tag ' . $request->name . ' has been created successfully');
    }
    public function edit($id)
    {
        $name = Tag::where('id', $id)->first();

        return view('inventory.master.tag.edit', compact('name'));
    }
    public function update(Request $request, $id)
    {


        $validated = $request->validate([
            'name' => 'required|min:2|max:100',
        ]);

        Tag::where('id', $id)->update($validated);

        return redirect()->route('tags.index')->with('success', 'Tag has been updated successfully');
    }
    public function destroy($id)
    {
        Tag::where('id', $id)->delete();

        return redirect()->route('tags.index')->with('success', 'Tags has been Deleted successfully');
    }
}
