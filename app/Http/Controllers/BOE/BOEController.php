<?php

namespace App\Http\Controllers\BOE;

use RedBeanPHP\R;
use App\Models\BOE;
use League\Csv\Writer;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use AmazonPHP\SellingPartner\Model\MerchantFulfillment\Length;
use App\Models\Company\CompanyMaster;
use App\Services\BOE\BOEPdfReader;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToArray;

class BOEController extends Controller
{
    public $check_table = 0;
    public $count = 0;
    public $company_id;
    public $dataArray = [
        'hawb_number' => '',
        'date_of_arrival' => '',
        'courier_registration_number' => '',
        'name_of_the_authorized_courier' => '',
        'name_of_consignor' => '',
        'name_of_consignee' => '',
        'rateof_exchange' => '',
        'Duty' => '',
        'SWsrchrg' => '',
        'insurance' => '',
        'IGST' => '',
        'duty_rs' => '',
        'interest' => '',
        'cbe_number' => '',
        'ctsh' => '',
        'quantity' => '',
        'descriptionof_goods' => '',
    ];
    public $dbheaders = [
        'hawb_number',
        'courier_registration_number',
        'name_of_the_authorized_courier',
        'name_of_consignor',
        'name_of_consignee',
        'rateof_exchange',
        'date_of_arrival',
        'duty_details',
        'insurance',
        'duty_rs',
        'interest',
        'cbe_number',
        'ctsh',
        'quantity',
        'descriptionof_goods'
    ];

    public function index(Request $request)
    {
        
        if ($request->ajax()) {
            $user = Auth::user();
            $roles = ($user->roles->first()->name);
            $company_id = $user->company_id;
            $boe_data = BOE::when($roles != "Admin", function($query) use($company_id){
                $query-> where('company_id', $company_id);
            });

            return DataTables::of($boe_data)
            ->addIndexColumn()
            ->addColumn('duty', function ($duty) {
                if (isset($duty['duty_details'])) {

                    $duty = (json_decode($duty['duty_details']));
                    return $duty[0]->DutyAmount;
                }
            })
            ->addColumn('swsrchrg', function ($swchar) {
                if (isset($swchar['duty_details'])) {
                    $swchar = (json_decode($swchar['duty_details']));
                    return $swchar[2]->DutyAmount;
                }
            })
            ->addColumn('igst', function ($igst) {
                if (isset($igst['duty_details'])) {
                    $igst = (json_decode($igst['duty_details']));
                    return $igst[3]->DutyAmount;
                }
            })
            ->rawColumns(['duty', 'swsrchrg', 'igst'])
            ->make(true);
        }
        return view('BOEpdf.index');
    }

    public function BOEPdfUploadView()
    {
        return view('BOEpdf.bulkuploadpdf');
    }

    public function ReadFromFile()
    {
        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

        $year = date('Y');
        $month = date('F');
        $user = Auth::user();
        $company_id = $user->company_id;
        $user_id = $user->id;
        $file_path = 'BOE/' . $company_id . '/' . $year . '/' . $month;
        $path = (storage_path('app/' . $file_path));
        $files = (scandir($path));
        $pdfReader = new BOEPdfReader();

        foreach ($files as $key => $file) {
            if ($key > 1) {
                $storage_path = $path . '/' . $file;
                $pdfParser = new Parser();
                $pdf = $pdfParser->parseFile($storage_path);
                $content = $pdf->getText();

                $pdfReader->BOEPDFReader($content, $file_path . '/' . $file, $company_id, $user_id);
            }
        }
    }

    public function BulkPdfUpload(Request $request)
    {
        $validatedData = $request->validate([
            'files' => 'required',
            'files.*' => 'mimes:pdf'
        ]);
        $host = config('database.connections.web.host');
        $dbname = config('database.connections.web.database');
        $port = config('database.connections.web.port');
        $username = config('database.connections.web.username');
        $password = config('database.connections.web.password');

        R::setup("mysql:host=$host;dbname=$dbname;port=$port", $username, $password);

        $pdfReader = new BOEPdfReader();
        $year = date('Y');
        $month = date('F');
        $user = Auth::user();
        $company_id = $user->company_id;
        $user_id = $user->id;

        $pdfList = [];

        foreach ($request->files as $key => $files) {

            foreach ($files as $keys => $file) {

                $fileName = $file->getClientOriginalName();
                $fileName = uniqid() . ($fileName);
                $desinationPath = 'BOE/' . $company_id . '/' . $year . '/' . $month . '/' . $fileName;
                Storage::put($desinationPath,  file_get_contents($file));

                $pdfList[] = $fileName;
            }
        }
        // reading saved file from storage

        $file_path = 'BOE/' . $company_id . '/' . $year . '/' . $month;
        $path = (storage_path('app/' . $file_path));
        foreach ($pdfList as $file_name) {

            $storage_path = $path . '/' . $file_name;
            $pdfParser = new Parser();
            $pdf = $pdfParser->parseFile($storage_path);
            $content = $pdf->getText();

            $pdfReader->BOEPDFReader($content, $file_path . '/' . $file_name, $company_id, $user_id);

        }
        return response()->json(["message" => "all file uploaded successfully"]);
        // return redirect('/BOE/index')->with('success', 'All PDF Imported successfully');
    }

    public function BOEExportToCSV()
    {
        $dbheaders = [
            'hawb_number',
            'courier_registration_number',
            'name_of_the_authorized_courier',
            'name_of_consignor',
            'name_of_consignee',
            'rateof_exchange',
            'date_of_arrival',
            'duty_details',
            'insurance',
            'duty_rs',
            'interest',
            'cbe_number',
            'ctsh',
            'quantity',
            'descriptionof_goods'
        ];

        $csvheaders = [
            'AWB no.',
            'BOE Date Of Arrival',
            'Courier Registration Number',
            'Name of the Authorized Courier',
            'Name of Consignor',
            'Name of Consignee',
            '(BOE) Booking Rate',
            'Duty',
            'SW Srchrg',
            'Insurance',
            'IGST',
            'Total (Duty+Cess+IGST)',
            'Interest',
            'CBX II NO',
            'HSN Code',
            'Qty',
            'Description'
        ];

        $user = Auth::user();
        $company_id = $user->company_id;
        $exportFilePath = "excel/downloads/BOE/$company_id/BOE_Details.csv";
        $chunk = 1000;
        BOE::select($dbheaders)->where('company_id', $company_id)->chunk($chunk, function ($records) use ($exportFilePath, $dbheaders, $csvheaders) {

            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $writer->insertOne($csvheaders);

            $records = $records->toArray();

            $recordsfinal = array_map(function ($datas) {

                if ($datas['duty_details']) {
                    $duty_details = (json_decode($datas['duty_details']));
                    $datas['Duty'] = $duty_details[0]->DutyAmount;
                    $datas['SWsrchrg'] = $duty_details[2]->DutyAmount;
                    $datas['IGST'] = $duty_details[3]->DutyAmount;
                }
                foreach ($datas as $key => $value) {

                    if ($key != 'duty_details') {

                        $this->dataArray[$key] = $value;
                    }
                }

                return $this->dataArray;
            }, $records);
            $writer->insertall($recordsfinal);
        });
        return redirect()->intended('/BOE/index')->with('success', 'BOE CSV Exported successfully');
    }

    public function BOEExportView(Request $request)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $role = $user->roles->first()->name;
        if ($role == 'Admin') {

            $companys = CompanyMaster::get();
        } else {

            $companys = CompanyMaster::where('id', $company_id)->get();
        }

        if ($request->ajax()) {

            $boe_data = $this->whereConditon($request);
            return DataTables::of($boe_data)
                ->addIndexColumn()
                ->addColumn('duty', function ($duty) {
                    if (isset($duty['duty_details'])) {

                        $duty = (json_decode($duty['duty_details']));
                        return $duty[0]->DutyAmount;
                    }
                })
                ->addColumn('swsrchrg', function ($swchar) {
                    if (isset($swchar['duty_details'])) {
                        $swchar = (json_decode($swchar['duty_details']));
                        return $swchar[2]->DutyAmount;
                    }
                })
                ->addColumn('igst', function ($igst) {
                    if (isset($igst['duty_details'])) {
                        $igst = (json_decode($igst['duty_details']));
                        return $igst[3]->DutyAmount;
                    }
                })
                ->rawColumns(['duty', 'swsrchrg', 'igst'])
                ->make(true);
        }

        return view('BOEpdf.export', compact(['companys', 'role']));
    }

    public function whereConditon($request, $dbheaders = NULL)
    {
        $company = $request->company;
        $date_of_arrival = $request->date_of_arrival;
        $challan_date = $request->challan_date;
        $upload_date = $request->upload_date;

        $boe = BOE::when($dbheaders, function ($query) use ($dbheaders) {
            $query->select($dbheaders);
        })
            ->when(!empty(trim($request->challan_date)), function ($query) use ($challan_date) {
                $date = $this->split_date($challan_date);
                $query->whereBetween('challan_date', [$date[0], $date[1]]);
            })
            ->when(!empty(trim($request->date_of_arrival)), function ($query) use ($date_of_arrival) {
               
                $date = $this->split_date($date_of_arrival);
                $query->whereBetween('date_of_arrival', [$date[0], $date[1]]);
            })
            ->when(!empty(trim($request->upload_date)), function ($query) use ($upload_date) {
                
                $date = $this->split_date($upload_date);
                $query->whereBetween('created_at', [$date[0], $date[1]]);
            })
            ->when($company, function ($query) use ($company) {

                $query->where('company_id', $company);
            });

        return $boe;
    }

    public function split_date($date_time)
    {
        $date = explode(' - ', $date_time);
        return [trim($date[0]), trim($date[1])];
    }

    public function BOEFilterExport(Request $request)
    {
        $user = Auth::user();
        $company_id = $request->company;

        $boe_data = $this->whereConditon($request, $this->dbheaders);

        $csvheaders = [
            'AWB no.',
            'BOE Date Of Arrival',
            'Courier Registration Number',
            'Name of the Authorized Courier',
            'Name of Consignor',
            'Name of Consignee',
            '(BOE) Booking Rate',
            'Duty',
            'SW Srchrg',
            'Insurance',
            'IGST',
            'Total (Duty+Cess+IGST)',
            'Interest',
            'CBX II NO',
            'HSN Code',
            'Qty',
            'Description'
        ];

        $company_id = $user->company_id;
        $exportFilePath = "excel/downloads/BOE/$company_id/BOE_Details.csv";
        $chunk = 1000;
        $boe_data->chunk($chunk, function ($records) use ($exportFilePath, $csvheaders) {

            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $writer->insertOne($csvheaders);

            $records = $records->toArray();

            $recordsfinal = array_map(function ($datas) {

                if ($datas['duty_details']) {
                    $duty_details = (json_decode($datas['duty_details']));
                    $datas['Duty'] = $duty_details[0]->DutyAmount;
                    $datas['SWsrchrg'] = $duty_details[2]->DutyAmount;
                    $datas['IGST'] = $duty_details[3]->DutyAmount;
                }
                foreach ($datas as $key => $value) {

                    if ($key != 'duty_details') {

                        $this->dataArray[$key] = $value;
                    }
                }

                return $this->dataArray;
            }, $records);

            $writer->insertall($recordsfinal);
        });
        return $this->Download_BOE();
        return redirect()->intended('/BOE/Export/view')->with('success', 'BOE CSV Exported successfully');
    }

    public function Download_BOE()
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $file_path = "excel/downloads/BOE/$company_id/BOE_Details.csv";
        if (Storage::exists($file_path)) {
            return Storage::download($file_path);
        }
        return 'file not exist';
    }

    public function Upload()
    {
        if (App::environment(['Production', 'Staging', 'production', 'staging'])) {

            $base_path = base_path();
            $command = "cd $base_path && php artisan pms:boe-upload-Do > /dev/null &";
            exec($command);

            Log::warning("Export asin command executed production  !!!");
        } else {

            // Log::warning("Export asin command executed local !");
            Artisan::call('pms:boe-upload-Do');
        }
        echo 'success';
        // return redirect()->back();
    }
}