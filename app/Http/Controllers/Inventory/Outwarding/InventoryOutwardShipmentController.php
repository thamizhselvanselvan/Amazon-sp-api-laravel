<?php

namespace App\Http\Controllers\Inventory\Outwarding;

use Illuminate\Http\Request;
use App\Models\Inventory\Inventory;
use App\Http\Controllers\Controller;

class InventoryOutwardShipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {

        return view('inventory.outward.shipment.index');
    }

    public function create()
    {
        // $source_lists = Source::get();
        return view('inventory.outward.shipment.create');
    }

    public function autocomplete(Request $request)
    {

        $data = Inventory::select("asin1")->distinct()
            ->where("asin1", "LIKE", "%{$request->asin}%")
            ->limit(50)
            ->get();

        return response()->json($data);
    }
}

    