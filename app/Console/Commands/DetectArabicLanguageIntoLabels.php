<?php

namespace App\Console\Commands;

use App\Models\Label;
use Illuminate\Console\Command;
use App\Models\ProcessManagement;
use Illuminate\Support\Facades\Log;
use App\Models\order\OrderItemDetails;

class DetectArabicLanguageIntoLabels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:detect-arabic-language-into-label {--order_id=}';

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

        $order_ids = $this->option('order_id');
        $order_ids = explode('_', $order_ids);

        $detect_arabic = [];
        $forTranslation = [];
        $class = "GoogleTranslate\GoogleTranslateArabicToEnglish";
        $queue_delay = 0;
        $queue_name = "GoogleTranslate";
        foreach ($order_ids as $order_no) {
            $check_order_id = Label::where('order_no', $order_no)
                ->where('detect_language', 0)
                ->get()->toArray();

            if ($check_order_id != null) {

                $address = OrderItemDetails::select('shipping_address')
                    ->where('amazon_order_identifier', $order_no)
                    ->get()
                    ->toArray();
                if ($address != null) {

                    $ship_address = json_encode($address[0]['shipping_address']);
                    $arabic_lang = preg_match("/u06/", $ship_address);

                    if ($arabic_lang == 1) {
                        $detect_arabic[] = $order_no;

                        $forTranslation = [
                            'order_no' => $order_no,
                            'shipping_address' => $address
                        ];
                    }


                    jobDispatchFunc($class, $forTranslation, $queue_name, $queue_delay);
                }
            }
        }
        // Label::upsert($detect_arabic, ['order_awb_no_unique'], ['order_no', 'detect_language']);

        Label::whereIn('order_no', $detect_arabic)
            ->update(['detect_language' => 1]);

        $command_end_time = now();
        ProcessManagementUpdate($pm_id, $command_end_time);
    }
}
