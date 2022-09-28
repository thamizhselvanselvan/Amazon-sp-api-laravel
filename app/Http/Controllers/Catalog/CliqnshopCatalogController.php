<?php

namespace App\Http\Controllers\Catalog;

use League\Csv\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CliqnshopCatalogController extends Controller
{
    public function catalogexport()
    {
        $cat_details = DB::connection('catalog')
            ->select("SELECT 
                cat.asin,
                cat.brand,
                cat.images,
                cat.item_name,
                price.usa_to_in_b2c as inprice 
            FROM catalognewuss AS cat
            join pricing_uss AS price
            on  cat.asin = price.asin
            LIMIT 100000         ");

        $headers = [
            'Category',
            'Sub-Category',
            'Brand',
            'ASIN',
            'Product Name',
            'short description',
            'long description',
            'Images',
            'Price',
            'price quantity',
            'price tax rate',
            'Attributese',
            'product variants',
            'Suggested Products',
            'Products bought together',
            'stock level',
            'date of back in stock'
        ];
        $exportFilePath = 'Cliqnshop/catalog.csv';
        if (!Storage::exists($exportFilePath)) {
            Storage::put($exportFilePath, '');
        }
        $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
        $writer->insertOne($headers);

        foreach($cat_details as $data)
        {
            $csv_array = [
                'Category' => null,
              'Sub-Category' => null,
            'Brand' =>$data->brand,
            'ASIN' => $data->asin,
            'Product Name' => $data->asin,
            'short description' => null,
            'long description' => null,
            'Images' => $data->images,
            'Price' => $data->inprice,
            'price quantity' => '1',
            'price tax rate' => '19',
            'Attributese' => null,
            'product variants' => null,
            'Suggested Products' => null,
            'Products bought together' => null,
            'stock level' => '500',
            'date of back in stock' => null
            ];

            $writer->insertOne($csv_array);
            
        }
        return redirect('/catalog/product')->with("success", "CliqnshopCatalog Price is Exporting please Wait for Few Minuts.");
    }

    public function exportdownload(Request $request)
    {
        // $catalogfiles = [];
        // $folder = $request->catalog;
        // $path = Storage::path($folder);
        // $files = glob($path."\*.csv"); 
        // return response()->json('success');  

        $catalogfiles = [];
        $folder = $request->catalog;
        $path = Storage::path($folder);
        $files = scandir($path);
       
        return response()->json($files);
    }
    public function DownloadCatalogcloqnshop()
    {
        $path = "Cliqnshop/catalog.csv";
        return Storage::download($path);
    }


    
}
