<?php

namespace App\Http\Controllers\label;

use ZipArchive;
use RedBeanPHP\R;
use App\Models\Label;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorHTML;

class labelManagementController extends Controller
{
    public function manage(Request $request)
    {
        if($request->ajax())
        {
            // $data = Label::orderBy('id', 'DESC')->get();
            $data = DB::connection('web')->select("select * from labels order by id DESC");
            
            foreach($data as $key => $value){
                $result[$key]['id'] = $value;
                
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($id) use ($result) {
                   
                    // $action1 = '<div class="pl-2"><input class="" type="checkbox" value='.$id['id'].' name="options[]" ></div>';
                    $action = '<div class="d-flex"><a href="/label/pdf-template/' . $id->id .' " class="edit btn btn-success btn-sm" target="_blank"><i class="fas fa-eye"></i> View </a>';
                    $action .= '<div class="d-flex pl-2"><a href="/label/download-direct/' . $id->id .' " class="edit btn btn-info btn-sm"><i class="fas fa-download"></i> Download </a>';
                    return $action;
                })
                ->addColumn('check_box', function ($id) use ($result) {

                    $check_box = '<div class="pl-2"><input class="check_options" type="checkbox" value='. $id->id .' name="options[]" ></div>';
                    return $check_box;
                })
                ->rawColumns(['action','check_box'])
                ->make(true);
            
        }
        return view('label.manage');
    }

    public function showTemplate($id)
    {
        // $results = Label::where('id', $id)->get();
        $results = DB::connection('web')->select("select * from labels where id = '$id' ");
        $awb_no = $results[0]->awb_no;
       
        $generator = new BarcodeGeneratorHTML();
        $bar_code = $generator->getBarcode('290306639908', $generator::TYPE_CODE_93);
        return view('label.labelTemplate', compact('results','bar_code', 'awb_no'));
    }
    public function ExportLabel(Request $request)
    {
        $url = $request->url;
        $awb_no = $request->awb_no;
        $file_path = 'label/label'. $awb_no. '.pdf';
        // po($file_path);
        // exit;
        if(!Storage::exists($file_path)){
            Storage::put($file_path, '');
        }
        $exportToPdf = storage::path($file_path);
        Browsershot::url($url)
        // ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
        ->format('A6')
        ->showBackground()
        ->savePdf($exportToPdf);

        return response()->json(['Save pdf sucessfully']);
    }

    public function downloadLabel($awb_no)
    {
        return Storage::download('label/label'. $awb_no. '.pdf');
    }

    public function DownloadDirect($id)
    {
        // $result = Label::where('id', $id)->get();
        $result = DB::connection('web')->select("select * from labels where id = '$id' ");
        $awb_no = $result[0]->awb_no;
        $file_path = 'label/label'. $awb_no. '.pdf';

        if(!Storage::exists($file_path))
        {
            Storage::put($file_path, '');
        }
        $exportToPdf = storage::path($file_path);
        $currentUrl = URL::current();
        $url = str_replace('download-direct', 'pdf-template', $currentUrl);
        
        Browsershot::url($url)
        // ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
        ->format('A6')
        ->showBackground()
        ->savePdf($exportToPdf);

        return $this->downloadLabel($awb_no);
    }

    public function PrintSelected($id)
    {
        $allid = explode('-', $id);
        foreach($allid as $id)
        {
            // $results []= Label::where('id', $id)->get();
            $results [] = DB::connection('web')->select("select * from labels where id ='$id' ");
        }
        // po($results);
        $generator = new BarcodeGeneratorHTML();
        $bar_code = $generator->getBarcode('290306639908', $generator::TYPE_CODE_39);

       return view('label.multipleLabel', compact('results', 'bar_code'));
    }
    
    public function DownloadSelected(Request $request)
    {
        $passid = $request->id;
        $currenturl =  URL::current();
        
        $excelid = explode('-', $passid);
        
        foreach($excelid as $getId)
        {
            // $id = Label::where('id', $getId)->get();
            $id = DB::connection('web')->select("select * from labels where id = '$getId' ");
            
            foreach($id as $key => $value)
            {
                $awb_no = $value->awb_no;
                $url = str_replace('select-download', 'pdf-template', $currenturl. '/'. $getId);
                $path = 'label/label'.$awb_no.'.pdf';
                if(!Storage::exists($path)) {
                    Storage::put($path, '');
                }
                $exportToPdf = storage::path($path);
                Browsershot::url($url)
                // ->setNodeBinary('D:\laragon\bin\nodejs\node-v14\node.exe')
                ->format('A6')
                ->showBackground()
                ->savePdf($exportToPdf);

                $saveAsPdf [] = 'label'.$awb_no .'.pdf';
            }
        }

        return response()->json($saveAsPdf);
    }

    public function zipDownload( $arr)
    { 
        // po($arr);
        $replace = explode(',', $arr);
        $zip = new ZipArchive;
        $path = 'label/zip/'.'label.zip';
        $fileName = Storage::path('label/zip/'.'label.zip');
        Storage::delete($path);
        if(!Storage::exists($path))
        {
            Storage::put($path, '');
        }
        if($zip->open($fileName, ZipArchive::CREATE) === TRUE)
        {
            foreach($replace as $key => $value)
            {
                $path = Storage::path('label/'.$value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
        return response()->download($fileName);
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

            $label = R::dispense('labels');
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
