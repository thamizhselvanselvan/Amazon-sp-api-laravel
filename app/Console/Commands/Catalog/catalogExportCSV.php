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
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:catalog-export-csv {country_code}';

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

        $table_name = 'catalog' . strtolower($this->country_code) . 's';
        $modal_table = table_model_create(country_code: $this->country_code, model: 'Catalog', table_name: 'catalog');
        $modal_table->orderBy('id')->chunk($chunk, function ($result) {

            if ($this->count == 1) {
                $this->file_path = "excel/downloads/catalog/" . $this->country_code . "/Catalog-export" . $this->country_code . $this->offset . ".csv";
                $this->csv_files[] = "Catalog-export" . $this->country_code . $this->offset . ".csv";

                if (!Storage::exists($this->file_path)) {
                    Storage::put($this->file_path, '');
                }

                $this->writer = Writer::createFromPath(Storage::path($this->file_path, 'w'));
                $header = [
                    'asin',
                    'source',
                    'binding',
                    'brand',
                    'color',
                    'item_dimensions',
                    'is_adult_product',
                    'is_autographed',
                    'is_memorabilia',
                    'label',
                    'manufacturer',
                    'material_type',
                    'model',
                    'package_dimensions',
                    'package_quantity',
                    'part_number',
                    'product_group',
                    'product_type_name',
                    'publisher',
                    'size',
                    'small_image',
                    'studio',
                    'title',
                    'display_size',
                    'languages',
                    'department',
                    'list_price',
                    'creator',
                    'number_of_items',
                    'warranty',
                    'flavor',
                    'platform',
                    'publication_date',
                    'release_date',
                    'hazardous_material_type',
                    'system_memory_size',
                    'item_part_number',
                    'product_type_subcategory',
                    'scent',
                    'manufacturer_parts_warranty_description',
                    'number_of_discs',
                    'manufacturer_maximum_age',
                    'manufacturer_minimum_age',
                    'hand_orientation',
                    'artist',
                    'audience_rating',
                    'format',
                    'genre',
                    'clasp_type',
                    'author',
                    'edition',
                    'chain_type',
                    'metal_type',
                    'total_diamond_weight',
                    'pegi_rating',
                    'region_code',
                    'metal_stamp',
                    'maximum_resolution',
                    'operating_system',
                    'size_per_pearl',
                    'optical_zoom',
                    'running_time',
                    'hardware_platform',
                    'shaft_material',
                    'band_material_type',
                    'processor_count',
                    'theatrical_release_date',
                    'number_of_pages',
                    'system_memory_type',
                    'gem_type',
                    'hard_disk_interface',
                    'hard_disk_size',
                    'media_type',
                ];
                $this->writer->insertOne($header);
            }
            foreach ($result as $value) {
                $records[] = [
                    'asin' => $value->asin,
                    'source' => $value->source,
                    'binding' => $value->binding,
                    'brand' => $value->brand,
                    'color' => $value->color,
                    'item_weight' => getWeight($value->item_dimensions),
                    'is_adult_product' => $value->is_adult_product,
                    'is_autographed' => $value->is_autographed,
                    'is_memorabilia' => $value->is_memorabilia,
                    'label' => $value->label,
                    'manufacturer' => $value->manufacturer,
                    'material_type' => $value->material_type,
                    'model' => $value->model,
                    'package_dimensions' => $value->package_dimensions,
                    'package_quantity' => $value->package_quantity,
                    'part_number' => $value->part_number,
                    'product_group' => $value->product_group,
                    'product_type_name' => $value->product_type_name,
                    'publisher' => $value->publisher,
                    'size' => $value->size,
                    'small_image' => $value->small_image,
                    'studio' => $value->studio,
                    'title' => $value->title,
                    'display_size' => $value->display_size,
                    'languages' => $value->languages,
                    'department' => $value->department,
                    'list_price' => $value->list_price,
                    'creator' => $value->creator,
                    'number_of_items' => $value->number_of_items,
                    'warranty' => $value->warranty,
                    'flavor' => $value->flavor,
                    'platform' => $value->platform,
                    'publication_date' => $value->publication_date,
                    'release_date' => $value->release_date,
                    'hazardous_material_type' => $value->hazardous_material_type,
                    'system_memory_size' => $value->system_memory_size,
                    'item_part_number' => $value->item_part_number,
                    'product_type_subcategory' => $value->product_type_subcategory,
                    'scent' => $value->scent,
                    'manufacturer_parts_warranty_description' => $value->manufacturer_parts_warranty_description,
                    'number_of_discs' => $value->number_of_discs,
                    'manufacturer_maximum_age' => $value->manufacturer_maximum_age,
                    'manufacturer_minimum_age' => $value->manufacturer_minimum_age,
                    'hand_orientation' => $value->hand_orientation,
                    'artist' => $value->artist,
                    'audience_rating' => $value->audience_rating,
                    'format' => $value->format,
                    'genre' => $value->genre,
                    'clasp_type' => $value->clasp_type,
                    'author' => $value->author,
                    'edition' => $value->edition,
                    'chain_type' => $value->chain_type,
                    'metal_type' => $value->metal_type,
                    'total_diamond_weight' => $value->total_diamond_weight,
                    'pegi_rating' => $value->pegi_rating,
                    'region_code' => $value->region_code,
                    'metal_stamp' => $value->metal_stamp,
                    'maximum_resolution' => $value->maximum_resolution,
                    'operating_system' => $value->operating_system,
                    'size_per_pearl' => $value->size_per_pearl,
                    'optical_zoom' => $value->optical_zoom,
                    'running_time' => $value->running_time,
                    'hardware_platform' => $value->hardware_platform,
                    'shaft_material' => $value->shaft_material,
                    'band_material_type' => $value->band_material_type,
                    'processor_count' => $value->processor_count,
                    'theatrical_release_date' => $value->theatrical_release_date,
                    'number_of_pages' => $value->number_of_pages,
                    'system_memory_type' => $value->system_memory_type,
                    'gem_type' => $value->gem_type,
                    'hard_disk_interface' => $value->hard_disk_interface,
                    'hard_disk_size' => $value->hard_disk_size,
                    'media_type' => $value->media_type
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
        $path = "excel/downloads/catalog/" . $this->country_code . "/zip/Catalog" . $this->country_code . ".zip";
        $file_path = Storage::path($path);

        if (!Storage::exists($path)) {
            Storage::put($path, '');
        }

        if ($zip->open($file_path, ZipArchive::CREATE) === TRUE) {
            foreach ($this->csv_files as $key => $value) {
                $path = Storage::path('excel/downloads/catalog/' . $this->country_code . '/' . $value);
                $relativeNameInZipFile = basename($path);
                $zip->addFile($path, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }
}
