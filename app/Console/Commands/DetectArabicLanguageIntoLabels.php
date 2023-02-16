<?php

namespace App\Console\Commands;

use App\Models\Label;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use App\Models\order\OrderItemDetails;
use Illuminate\Support\Facades\Storage;

class DetectArabicLanguageIntoLabels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:detect-arabic-language-into-label';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect arabic language into label table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $process_manage = [
            'module'             => 'Language Detection For Label.',
            'description'        => 'Detect arabic language and flag into label table.',
            'command_name'       => 'mosh:detect-arabic-language-into-label',
            'command_start_time' => now(),
        ];
        $process_management_id = ProcessManagement::create($process_manage)->toArray();
        $pm_id = $process_management_id['id'];

        $detect_arabic = [];
        $forTranslation = [];
        $class = "GoogleTranslate\GoogleTranslateArabicToEnglish";
        $queue_delay = 0;
        $queue_name = "default";

        $path = "label/label.csv";
        $records = CSV_Reader($path);

        $order_id_array = [];
        $upsert_label = [];
        $qty_update = [];
        $qty_check = [];
        foreach ($records as $value) {

            $order_id = trim($value['OrderNo']);
            $order_item_id = trim($value['OrderItemId']);
            $bag_no = trim($value['BagNo']);

            $qty_check[] = $order_item_id;
            $order_id_array[] = $order_id;

            $qty_update[$order_item_id] = [
                'order_no' => $order_id,
                'order_item_id' => $order_item_id,
                'bag_no' =>  $bag_no,
                'qty' => 0
            ];
        }

        $new_order = Label::whereIn('order_no', $order_id_array)
            ->where('detect_language', 0)
            ->get(['order_no'])
            ->toArray();

        $ord_item_array = [];
        foreach ($new_order as $ords) {
            $ord_item_array[] = $ords['order_no'];
        }

        $address = OrderItemDetails::select(
            [
                'amazon_order_identifier',
                'shipping_address',
            ]
        )
            ->whereIn('amazon_order_identifier', $ord_item_array)
            ->get()
            ->toArray();

        foreach ($address as $val) {

            $order_no = $val['amazon_order_identifier'];
            $ship_address = json_encode($val['shipping_address']);
            $arabic_lang = preg_match("/u06/", $ship_address);

            if ($arabic_lang == 1) {
                $detect_arabic[] = $order_no;

                $forTranslation = [
                    'order_no' => $order_no,
                    'shipping_address' => $val['shipping_address']
                ];

                $upsert_label[] = $order_no;
                jobDispatchFunc($class, $forTranslation, $queue_name, $queue_delay);
            }
        }

        Label::whereIn('order_no', $upsert_label)
            ->update(['detect_language' => 1]);


        $this->getQuantityAndUpdate($qty_check, $qty_update);
        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }

    public function getQuantityAndUpdate($order_item_id, $qty_update)
    {
        $address = OrderItemDetails::select(
            [
                'amazon_order_identifier',
                'order_item_identifier',
                'quantity_ordered',
            ]
        )
            ->whereIn('order_item_identifier', $order_item_id)
            ->get()
            ->toArray();

        foreach ($address as $value) {

            if (array_key_exists($value['order_item_identifier'], $qty_update)) {
                $qty_update[$value['order_item_identifier']]['qty'] = $value['quantity_ordered'];
            }

            Label::upsert($qty_update, 'order_item_bag_unique', ['qty']);
        }
    }
}
