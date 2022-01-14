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
        $marketplace = 'A21TJRUUN4KGV';

        $titles = preg_split("/\r\n| |''|,/", $request->asinText);
        $newData = [];

        foreach ($titles as $title) {
            if (!empty(trim($title))) {
                $newData[] = trim($title);
            }
        }

        echo "<PRE>";

        // return $newData;
        $count = 0;
        $title = [];
        $sp_api = new SpApi;

        print_r($newData);

        foreach ($newData as $data) {

            $count++;
            if ($count > 5) {
                break;
            }

            $response = $sp_api->catalogApitest($marketplace, $data);
            $product = $response->getAttributeSets();
            $product = $product[0];
            /*
            print_r($product->getTitle());
            print_r($product->getListPrice());
            print_r($product->getPackageDimensions()->getHeight()->getValue());
            print_r($product->getPackageDimensions()->getHeight()->getunits());
            */
            print_r($product);

            exit;
            sleep(2);

            //DB::table('products')->insert(['ASIN' => $data, 'Title' => $title]);

            // return $sp_api->catalogApitest($marketplace, $data);
        }
    }
}
