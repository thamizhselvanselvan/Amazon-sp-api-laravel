<?php

namespace App\Http\Controllers;

use App\Services\SpApi;
use App\Models\Products;
use App\Models\SaveAsin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ClouSale\AmazonSellingPartnerAPI\Models\ProductPricing\Product;
use \RedBeanPHP\R as R;

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
        echo "<PRE>";

        if (file_exists('B073SBZ8YH.txt')) {
            echo 'reading from file';
            $response = json_decode(file_get_contents('B073SBZ8YH.txt'));

            print_r($response);

            $book = R::dispense("book");
            $book->author = "Santa Claus";
            $book->$$title = "Secrets of Christmas";
            $book->name = "gopal";
            $book->age = "22";
            $book->mobile = "1234";
            R::store($book);



            //print_r($response->AttributeSets->Brand);
        } else {
            echo 'reading from amazon';
            $sp_api = new SpApi;
            $response = $sp_api->catalogApitest($marketplace, 'B073SBZ8YH');
            file_put_contents('B073SBZ8YH.txt', json_encode($response));
        }

        //print_r($response);
        exit;


        R::setup('mysql:host=localhost:8001;dbname=sp_api', 'root', 'root');
        R::debug(TRUE);

        //$book  = R::find( 'book', ' id = 2 ');
        //$book = R::load('book', 3);

        $book = R::dispense("book");
        $book->author = "Santa Claus";
        $book->title = "Secrets of Christmas";
        $book->name = "gopal";
        $book->age = "22";
        $book->mobile = "1234";
        R::store($book);

        exit;



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
