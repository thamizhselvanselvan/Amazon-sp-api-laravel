<?php

namespace App\Http\Controllers\Inventory\Outwarding;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Inventory\Inventory;
use App\Http\Controllers\Controller;
use App\Models\Inventory\Destination;
use App\Models\Inventory\Outshipment;
use Yajra\DataTables\Facades\DataTables;

class InventoryOutwardShipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        
            if ($request->ajax()) {
    
                $data = Outshipment::select("ship_id", "destination_id")->distinct()->with(['destinations']);
    
    
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('destination_name', function ($data) {
                        return ($data->destinations) ? $data->destinations->name : " NA";
                    })
                    ->addColumn('action', function ($row) {
    
                        $actionBtn = '<div class="d-flex"><a href="/inventory/shipments/' . $row->id . '/edit" class="edit btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>';
                        $actionBtn .= '<button data-id="' . $row->id . '" class="delete btn btn-danger btn-sm ml-2"><i class="far fa-trash-alt"></i> Remove</button></div>';
                        return $actionBtn;
                    })
                    ->rawColumns(['destinations_name', 'action'])
                    ->make(true);
            }
    
    
        

        return view('inventory.outward.shipment.index');
    }

    public function create()
    {
         $destination_lists = Destination::get();
        return view('inventory.outward.shipment.create',compact(('destination_lists')));
    }

    public function autofinish(Request $request)
    {

        $data = Inventory::select("asin")->distinct()
            ->where("asin", "LIKE", "%{$request->asin}%")
            ->limit(50)
            ->get();

        return response()->json($data);
    }
    public function selectview(Request $request)
    {

        if ($request->ajax()) {

            return Inventory::query()->where('asin', $request->asin)->first();
        }
    }
    public function storeoutshipment(Request $request)
    {

        $shipment_id = random_int(1000, 9999);

        $createout = [];

        foreach ($request->asin as $key => $asin) {

            $createout[] = [
                "ship_id" => $shipment_id,
                "destination_id" => $request->destination,
                "asin" => $asin,
                "item_name" => $request->name[$key],
                "quantity" => $request->quantity[$key],
                "price" => $request->price[$key],
                "created_at" => now(),
                "updated_at" => now()
            ];
        }

        Outshipment::insert($createout);

        return response()->json(['success' => 'Shipment has Created successfully']);
    }
    public function outwardingview(Request $request)
    {

        if ($request->ajax()) {

            $data = Outshipment::query()->with(['destinations']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('destination_name', function ($data) {
                    return ($data->destinations) ? $data->destinations->name : " NA";
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row['created_at'])->format('M d Y');
               
                })
                ->rawColumns(['destination_name', 'created_at'])
              
                ->make(true);
        }

        return view('inventory.outward.shipment.view');
    }
}

    