<?php

namespace App\Http\Controllers\label;

use RedBeanPHP\R;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Picqer\Barcode\BarcodeGeneratorHTML;

class labelManagementController extends Controller
{
    public function manage(Request $request)
    {
        if($request->ajax())
        {

        }
        return view('label.manage');
    }

    public function downloadExcelTemplate()
    {
        $path = public_path() . '/storage/LabelTemplate/Label_Excel_Template.xlsx';
        return response()->download($path);
    }

    public function upload()
    {
        return  view('label.upload');
    }

    public function uploadExcel(Request $request)
    {
        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

        foreach ($request->files as $key => $files) {

            foreach ($files as $keys => $file) {

                $fileName = $file->getClientOriginalName();
                $fileName = uniqid() . ($fileName);
            }
        }
        $data = Excel::toArray([], $file);
        $header_value = [];
        $excel_data = [];

        foreach ($data as $header) {
            foreach ($header as $key => $header_data) {
                if ($key == 0) {
                    foreach ($header_data as $headerKey => $excel_header) {
                        if ($excel_header) {
                            $header_value[$headerKey] = $excel_header;
                        }
                    }
                } else {
                    foreach ($header_data as $valueKey => $excel_value) {
                        if ($excel_value) {
                            $excel_data[$key][$valueKey] = $excel_value;
                        }
                    }
                }
            }
        }

        foreach ($excel_data as $data) {

            $label = R::dispense('label');
            $label->status = 0;
            foreach ($data as $key => $value) {
                if (isset($header_value[$key])) {
                    
                    $column = lcfirst($header_value[$key]);
                    $label->$column = $value;
                }
            }
            R::store($label);
        }

        return response()->json(["success" => "All file uploaded successfully"]);
    }

    public function labelTemplate()
    {
        $generator = new BarcodeGeneratorHTML();
        $bar_code = $generator->getBarcode('290306639908', $generator::TYPE_CODE_39);

        return view('label.template', compact('bar_code'));
    }
}
