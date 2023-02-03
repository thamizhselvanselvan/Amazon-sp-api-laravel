<?php

namespace App\Http\Controllers\Catalog;


use Illuminate\Http\Request;
use App\Models\FileManagement;
use App\Models\Catalog\PricingUs;
use App\Http\Controllers\Controller;
use App\Models\Catalog\ExchangeRate;
use Illuminate\Support\Facades\Auth;

class CatalogExchangeManagementController extends Controller
{
    public function index()
    {
        return view('Catalog.ExchangeRate.index');
    }

    public function CatalogUpdate(Request $request)
    {
        $exchange_rate = $request->validate([
            'source_destination'    =>  'required|in:ind_to_uae,ind_to_sg,ind_to_sa,usa_to_sg,usa_to_uae,usa_to_ind_b2c,usa_to_ind_b2b',
            'packaging' => 'required',
            'sp_commission' =>  'required',
            'base_weight'   =>  'required',
            'seller_commission' =>  'required',
            'excerise_rate'    =>  'required',
            'base_shipping_charge'  =>  'required',
            'duty_rate' =>  'required',
            'amazon_commission' =>  'required',
        ]);

        ExchangeRate::upsert($exchange_rate, ['source_destination'], [
            'source_destination',
            'base_weight',
            'base_shipping_charge',
            'packaging',
            'seller_commission',
            'duty_rate',
            'sp_commission',
            'excerise_rate',
            'amazon_commission',
        ]);
        return redirect('/catalog/exchange-rate')->with("success", "Records has been updated successfully !");
    }

    public function CatalogRecordAutoload(Request $request)
    {
        $source_destination = $request->option;
        $records = ExchangeRate::where('source_destination', $source_destination)->get()->toArray();

        return response()->json($records);
    }

    public function CatalogBuyBoxPriceRecalculate()
    {
        PricingUs::where('status', 1)->update(['status' => 0]);
        $user_id = Auth::user()->id;

        $file_info = [
            "user_id" => $user_id,
            "type" => "BUYBOX_PRICE_ReCALCULATE",
            "module" => "BUYBOX_PRICE_ReCALCULATE",
            "command_name" => "mosh:catalog-buybox-price-recalculate"

        ];
        FileManagement::create($file_info);
        fileManagement();

        return redirect('/catalog/exchange-rate')->with("success", "BuyBox price is re-calculating...");
    }
}
