<?php

namespace App\Http\Controllers\Inventory\Master\Rack;

use Illuminate\Http\Request;
use App\Models\Inventory\Bin;
use App\Models\Inventory\Shelve;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class InventoryBinController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Bin::query()->with(['shelves']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('shelves_count', function ($data) {
                    return ($data->shelves) ? $data->shelves->count() : 0;
                })
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="Edit_bin/' . $row->id . '" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['shelves_count', 'action'])
                ->make(true);
        }

        return view('inventory.master.racks.bin.index');
    }
    public function create()
    {
        $shelve_lists = Shelve::get();
        return view('inventory.master.racks.bin.add',compact('shelve_lists'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2|max:50',
            'depth' => 'required|min:2|max:50',
            'width' => 'required|min:2|max:50',
            'height' => 'required|min:2|max:50',
            'zone'  => 'required|min:2|max:50'
        ]);
        $shelve_exists = Shelve::where('id', $request->shelve_id)->exists();

        if(!$shelve_exists) {
            return redirect()->route('bins.create')->with('error', 'Selected shelve id invalid');
        }
        Bin::create([
        'shelve_id' => $request->shelve_id,
        'name' => $request->name,
        'depth' => $request->depth,
        'width' => $request->width,
        'height' => $request->height,
        'zone' => $request->zone
        ]);
        return redirect()->route('bins.index')->with('success', 'Bin  has been created successfully');
    }


}
