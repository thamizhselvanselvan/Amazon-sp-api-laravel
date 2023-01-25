<?php

namespace App\Http\Controllers\Catalog;

use League\Csv\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        return view('Cliqnshop.category');
    }

    public function export(Request $request)
    // public function export()
    {
        $request->validate([
            'category_csv' => 'required',
        ]);

        $file = file_get_contents($request->category_csv);

        $path = "Cliqnshop/category_import/category_asin.csv";

        // Storage::put($path, $file);

        $csv_data_temp = ['asin', 'display_name', 'classification_id', 'tree'];

        if (!Storage::exists($path)) {
            return false;
        } else {

            $csv_data = CSV_Reader($path);
            foreach ($csv_data as $data) {
                $asin[] = $data['ASIN'];
            }
            // po($asin);
            // exit;
            $count = count($asin);
            $result = DB::connection('catalog')->table('catalognewuss')
                ->whereIn('asin', $asin)->pluck('browse_classification', 'asin');

            $csv_data = [];
            $c_id = [];
            foreach ($result as $key => $value) {

                $data = json_decode($value, true);
                if(isset($data['classificationId']) && isset( $data['displayName']))
                {
                    $c_id[] = $data['classificationId'];
                    $csv_data[$data['classificationId']] = [
                        'asin' => $key,
                        'classificationId' => $data['classificationId'],
                        'd_name' => $data['displayName'],
                    ];
                }
                else
                {
                    $csv_data[$key]['asin']=$key;
                }
            }

            $treename = DB::connection('catalog')->table('categoriestree')
                ->whereIn('browseNodeId', $c_id)->pluck('Tree', 'browseNodeId')->toArray();

            foreach ($treename as $key => $results) {

                if (array_key_exists($key, $csv_data)) {
                    $csv_data[$key]['tree'] = $results;
                }
            }
           
            $headers =['Asin', 'Category Id','Category Name', 'Tree'];
            $exportFilePath = 'test/Categories.csv';
            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $writer->insertOne($headers);
            $writer->insertAll($csv_data);

            return Storage::download($exportFilePath);
        }
    }
}
