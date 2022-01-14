<?php

namespace App\Http\Controllers;

use App\Services\SpApi;
use App\Models\Products;
use App\Models\SaveAsin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ClouSale\AmazonSellingPartnerAPI\Models\ProductPricing\Product;

class SaveAsinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('show');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function show(Request $request)
    {
        $marketplace = 'A1PA6795UKMFR9';

        $titles = preg_split("/\r\n| |''|,/", $request->asinText);
        $newData = [];

        foreach ($titles as $title) {
            if (!empty(trim($title))) {
                $newData[] = trim($title);
            }
        }

        // return $newData;
        $count = 0;
        $title = [];
        $sp_api = new SpApi;
        foreach ($newData as $data) {
            $count++;
            if ($count > 5) {
                break;
            }

            $title = $sp_api->catalogApitest($marketplace, $data);

            DB::table('products')->insert([
                'ASIN' => $data,
                'Title' => $title

            ]);

            // return $sp_api->catalogApitest($marketplace, $data);
        }

        return $title;
    }
}
