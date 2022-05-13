<?php

namespace App\Http\Controllers\inventory\inwarding;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Inventory\Source;
use App\Models\Inventory\Shipment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class InventoryShipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Shipment::select("ship_id","source_id")->distinct()->with(['sources']);

          
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('source_name', function ($data) {
                    return ($data->sources) ? $data->sources->name : " NA";
                })
                ->addColumn('action', function ($row) {

                    $actionBtn = '<div class="d-flex"><a href="/inventory/shipments/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                    $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                    return $actionBtn;
                })
                ->rawColumns(['source_name', 'action'])
                ->make(true);
        }


        return view('inventory.inward.shipment.index');
    }
    public function create()
    {
        $source_lists = Source::get();
        return view('inventory.inward.shipment.create', compact('source_lists'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'Ship_id' => 'required|min:1|max:100',
            'asin' => 'required|min:1|max:100',
        ]);

        $source_exists = Source::where('id', $request->source_id)->exists();

        if (!$source_exists) {
            return redirect()->route('shipments.create')->with('error', 'Selected Source id invalid');
        }


        Shipment::create([
            'Ship_id' => $request->Ship_id,
            'source_id' => $request->source_id,
        ]);

        return redirect()->route('shipments.index')->with('success', 'Shipment ' . $request->Ship_id . ' has been created successfully');
    }
    public function edit($id)
    {

        $name = Shipment::where('id', $id)->first();

        return view('inventory.inward.shipment.edit', compact('name'));
    }


    public function update(Request $request, $id)
    {

        $validated = $request->validate([
            'ship_id' => 'required|min:2|max:100',
            'asin' => 'required|min:2|max:100',
        ]);

        Shipment::where('id', $id)->update($validated);

        return redirect()->route('shipments.index')->with('success', 'Shipment has been updated successfully');
    }

    public function destroy($id)
    {
        Shipment::where('id', $id)->delete();

        return redirect()->route('shipments.index')->with('success', 'Shipment has been Deleted successfully');
    }
    public function createView(Request $request)
    {

        return view('inventory.inward.shipment.create');
    }

    public function autocomplete(Request $request)
    {

        $data = Product::select("asin1")->distinct()
            ->where("asin1", "LIKE", "%{$request->asin}%")
            ->limit(50)
            ->get();

        return response()->json($data);
    }

    public function selectView(Request $request)
    {

        if ($request->ajax()) {

            return Product::query()->where('asin1', $request->asin)->first();
        }
    }


    public function storeshipment(Request $request)
    {
 
        $ship_id = random_int(1000, 9999);
  
        $create = [];

       foreach($request->asin as $key => $asin) {

            $create[] = [
                "Ship_id" => $ship_id,
                "source_id" => $request->source,
                "asin" => $asin,
                "item_name" => $request->name[$key],
                "quantity" => $request->quantity[$key],
                "price" => $request->price[$key],
                "created_at" => now(),
                "updated_at" => now()
            ];
            
       }


        // $source_exists = Source::where('id', $request->source_id)->exists();

        // if (!$source_exists) {
        //     return redirect()->route('shipments.index')->with('error', 'Selected Source is invalid');
        // }

        Shipment::insert($create);
        
        return response()->json(['success' => 'Shipment has Created successfully']);
    }
}
