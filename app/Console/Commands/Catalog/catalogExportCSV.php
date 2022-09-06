<?php

namespace App\Console\Commands\Catalog;

use ZipArchive;
use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class catalogExportCSV extends Command
{
    private $offset = 0;
    private $count = 1;
    private $country_code;
    private $remender;
    private $writer;
    private $csv_files = [];
    private $file_path;
    private $priority;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog-export-csv {priority} {country_code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Catalog export into csv file';

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
        $total_csv = 1000000;
        $chunk = 100000;
        $this->remender = $total_csv / $chunk;
        $this->country_code = $this->argument('country_code');

        $this->priority = $this->argument('priority');

        Log::alert($this->priority);

        $header = [
            'asin',
            'source',
            'dimensions',
            'images',
            'product_types',
            'brand',
            'color',
            'item_classification',
            'item_name',
            'style',
            'website_display_group',
            'manufacturer'
        ];

        $asin_desti = 'asin_destination_' . strtolower($this->country_code) . 's';
        $asin_cat = 'catalognew' . strtolower($this->country_code) . 's';

        $table_name = table_model_create(country_code: $this->country_code, model: 'Asin_destination', table_name: 'asin_destination_');

        $table_name->select($header)
            ->where('priority', $this->priority)
            ->join($asin_cat, $asin_desti . '.asin', '=', $asin_cat . '.asin')
            ->chunk($chunk, function ($result) use ($header) {

                Log::info(json_encode($result));

                if ($this->count == 1) {

                    $csv_header = [
                        'Asin',
                        'Source',
                        'Weight',
                        'Images_1',
                        'Images_2',
                        'Product_types',
                        'Brand',
                        'Color',
                        'Item_classifications',
                        'Item_name',
                        'Style',
                        'Website_display_group',
                        'Manufacturer'
                    ];

                    $this->file_path = "excel/downloads/catalog/" . $this->country_code . "/Priority" . $this->priority . "/Catalog-export" . $this->country_code . $this->offset . ".csv";
                    $this->csv_files[] = "Catalog-export" . $this->country_code . $this->offset . ".csv";

                    if (!Storage::exists($this->file_path)) {
                        Storage::put($this->file_path, '');
                    }

                    $this->writer = Writer::createFromPath(Storage::path($this->file_path, 'w'));
                    $this->writer->insertOne($csv_header);
                }

                foreach ($result as $value) {
                    $weight = '0.5';
                    $product_type  = '';
                    $images1  = '';
                    $images2  = '';

                    if (isset(json_decode($value->dimensions)[0]->package->weight->value)) {

                        $weight = json_decode($value->dimensions)[0]->package->weight->value;
                    }

                    if (isset(json_decode($value->product_types)[0]->productType)) {
                        $product_type = json_decode($value->product_types)[0]->productType;
                    }
                    if (isset(json_decode($value->images)[0]->images[0]->link)) {
                        $images1 = json_decode($value->images)[0]->images[0]->link;
                    }
                    if (isset(json_decode($value->images)[0]->images[1]->link)) {
                        $images2 = json_decode($value->images)[0]->images[1]->link;
                    }

                    $records[] = [
                        'asin' => $value->asin,
                        'source' => $value->source,
                        'weight' => $weight,
                        'images1' => $images1,
                        'images2' => $images2,
                        'product_types' => $product_type,
                        'brand' => $value->brand,
                        'color' => $value->color,
                        'item_classifications' => $value->item_classifications,
                        'item_name' => $value->item_name,
                        'style' => $value->style,
                        'website_display_group' => $value->website_display_group,
                        'manufacturer' => $value->manufacture,
                    ];
                }
                $this->writer->insertAll($records);

                if ($this->remender == $this->count) {
                    ++$this->offset;
                    $this->count = 1;
                } else {
                    ++$this->count;
                }
            });

        $zip = new ZipArchive;
        $path = "excel/downloads/catalog/" . $this->country_code . "/Priority" . $this->priority . "/zip/Catalog" . $this->country_code . ".zip";
        $file_path = Storage::path($path);

        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }
        if ($zip->open($file_path, ZipArchive::CREATE) === TRUE) {
            foreach ($this->csv_files as $key => $value) {
                $path = Storage::path('excel/downloads/catalog/' . $this->country_code . '/Priority' . $this->priority . '/' . $value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }
}
