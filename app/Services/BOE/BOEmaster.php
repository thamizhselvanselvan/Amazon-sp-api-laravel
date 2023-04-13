<?php

namespace App\Services\BOE;

use DateTime;
use Carbon\Carbon;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;
use App\Services\BOE\BOEPdefreader2018;

class BOEmaster
{
    public function BOEmanage($content, $storage_path, $company_id, $user_id)
    {
        $content_txt = $content;
        $content_array = preg_split('/[\r\n|\t|,]/', $content, -1, PREG_SPLIT_NO_EMPTY);

        if (isset($content_array[0]) && str_contains($content_array[0], 'Form Courier Bill Of Entry')) {

            $BOEPDFMasterold = $content_array;
            $Boecheck = $BOEPDFMasterold;

            foreach ($content_array as $key => $BOEPDFData) {
                if ($BOEPDFData == 'Date of Arrival:') {
                    $date2018  = $BOEPDFMasterold[$key + 1];
                    $date_Boe = Carbon::createFromFormat('d/m/Y', $date2018)->format('Y');
                    if ($date_Boe == '2018' || $date_Boe == '2019' || $date_Boe == '2020') {
                        $get = new BOEPdefreader2018;
                        $get->BOEPDFReaderold($content_txt, $storage_path, $company_id, $user_id);
                    }

                    break 1;
                } else if ($BOEPDFData == 'Airport of Shipment :') {

                    $date = $Boecheck[$key - 2];
                    $date_Boe = Carbon::createFromFormat('d/m/Y', $date)->format('Y');

                    if ($date_Boe == '2018' || $date_Boe == '2019' || $date_Boe == '2020') {
                        $get2018 = new BOEPdefreader2018;
                        $get2018->BOEPDFReaderold($content_txt, $storage_path, $company_id, $user_id);
                    } else  if ($date_Boe == '2022' || $date_Boe == '2023') {
                        $get2022 = new BOEPdfReader;
                        $get2022->BOEPDFReader($content_txt, $storage_path, $company_id, $user_id);
                    }
                    // else  if ($date_Boe == '2019') {
                    //     $get2019 = new BOEPdefreader2018;
                    //     $get2019->BOEPDFReaderold($content_txt, $storage_path, $company_id, $user_id);
                    // }

                    break 1;
                }
            }
            return true;
        } else {
            Log::alert('Invalid Boe File');
            // slack_notification('app360', 'BOE', 'Invalid BOE Found');
            return false;
        }
    }
}
