<?php

namespace App\Services\BOE;

use DateTime;
use Carbon\Carbon;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;
use App\Services\BOE\BOEPdefreader2018;

class BOEMaster
{
    public function BOEmanage($content, $storage_path, $company_id, $user_id)
    {
        // $pdfParser = new Parser();
        // $path = 'D:\BOE\BOE 2018\GO TECH BOE\1\957299837.pdf';
        // $path = 'D:\BOE\BOE 2018\GO TECH BOE\1\957299835.pdf';
        // $path = 'D:\BOE\BOE 2018\GO TECH BOE\1\957302273.pdf';
        // $path = 'D:\BOE\967102452.pdf';
        // $path = 'D:\BOE\957299835.pdf';
        // $path = 'D:\BOE\967102640.pdf';
        // $path = 'D:\BOE\957302469.pdf';
        // $pdfParser = new Parser();
        // $pdf = $pdfParser->parseFile($path);
        // $content = $pdf->getText();
        $content = preg_split('/[\r\n|\t|,]/', $content, -1, PREG_SPLIT_NO_EMPTY);
        // dd($content);
        $date_Boe = '';
        // $storage_path = '';
        // $company_id = '';
        // $user_id = '';

        if ($content[0] == "Form Courier Bill Of Entry -XIII (CBE-XIII)") {
            $BOEPDFMasterold = $content;
            $Boecheck = $BOEPDFMasterold;

            foreach ($content as $key => $BOEPDFData) {
                if ($BOEPDFData == 'Date of Arrival:') {
                    $date2018  = $BOEPDFMasterold[$key + 1];
                    $date_Boe = Carbon::createFromFormat('d/m/Y', $date2018)->format('Y');
                    if ($date_Boe == '2018') {
                        $get = new BOEPdefreader2018;
                        $get->BOEPDFReaderold($content, $storage_path, $company_id, $user_id);
                    }
                }
                // else  if ($BOEPDFData == "Date Of Arrival") {

                //     $date2022  = $BOEPDFMasterold[$key + 7];
                //     $date_Boe = Carbon::createFromFormat('d/m/Y', $date2022)->format('Y');
                //     if ($date_Boe == '2022') {

                //         $get2022 = new BOEPdfReader;
                //         $get2022->BOEPDFReader($content, $storage_path, $company_id, $user_id);
                //     }
                // }
                else if ($BOEPDFData == 'Time Of Arrival') {
                    $check_key = $key;
                    $count = 0;
                    $offset = 0;
                    while ($Boecheck[$check_key] != 'Airport of Shipment :') {
                        $check_key++;
                        $count++;
                    }
                    $append = $count - 6;
                    while ($offset != $append) {

                        $check_key++;
                        $offset++;
                    }

                    $date = $Boecheck[$key + $offset + 4];
                    $date_Boe = Carbon::createFromFormat('d/m/Y', $date)->format('Y');

                    if ($date_Boe == '2018') {
                        $get = new BOEPdefreader2018;
                        $get->BOEPDFReaderold($content, $storage_path, $company_id, $user_id);
                    } else  if ($date_Boe == '2022') {
                        $get2022 = new BOEPdfReader;
                        $get2022->BOEPDFReader($content, $storage_path, $company_id, $user_id);
                    }
                }
            }
        }
    }
}
