<?php

namespace App\Services\GoogleTranslateServices;

use App\Models\GoogleTranslate;
use App\Models\Label;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Translate\V2\TranslateClient;

class ArabicToEnglish
{
    public function TranslateAPI($records)
    {
        $AllRecords = [];
        $LabelUpdate = [];
        $translate = new TranslateClient([
            'key' => config('app.google_translate_key')
        ]);

        $AllRecords['amazon_order_identifier'] = $records['order_no'];
        foreach ($records['shipping_address'][0]['shipping_address'] as $key1 => $arabic) {

            if (preg_match('/u06/', json_encode($arabic)) == 1) {

                $translatedText = $translate->translate($arabic, [
                    'target' => 'en'
                ]);
                if ($key1 != 'CountryCode' && $key1 != 'Phone' && $key1 != 'AddressType' && $key1 != 'country') {

                    $AllRecords[strtolower($key1)] = $translatedText['text'];
                }
            }
        }

        GoogleTranslate::upsert($AllRecords, ['amazon_order_id_unique'], ['amazon_order_identifier', 'name', 'addressline1', 'addressline2', 'city', 'county']);
        // Label::upsert($LabelUpdate, ['order_awb_no_unique'], ['order_no', 'detect_language']);
        Label::where('order_no', $records['order_no'])->update(['detect_language' => 2]);
    }
}
