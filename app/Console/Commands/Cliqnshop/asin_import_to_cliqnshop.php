<?php

namespace App\Console\Commands\Cliqnshop;

use League\Csv\Writer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SellingPartnerApi\Model\ListingsV20200901\Issue;

class asin_import_to_cliqnshop extends Command
{
    private $offset = 0;
    private $count = 1;
    private $writer;
    private $file_path;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:export_catalog_imported_asin {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comand will generate a csv and also Insert Catalog Data into Cliqnshop Table';

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

        $file_path = $this->argument('path');
        $csv_data =  CSV_Reader($file_path);

        foreach ($csv_data as $data) {
            $asin[] = ($data['ASIN']);
           
        }

        $headers = [
            'catalognewuss.asin',
            'catalognewuss.brand',
            'catalognewuss.images',
            'catalognewuss.item_name',
            'catalognewuss.browse_classification',
            'catalognewuss.dimensions',
            'catalognewuss.attributes',
            'catalognewuss.color',
            'pricing_uss.usa_to_in_b2c',
            'pricing_uss.us_price',
            'pricing_uss.usa_to_uae',

        ];

        $csv_header = [
            'item code',
            'item label',
            'item type',
            'item status',
            'text type',
            'text content',
            'text type',
            'text content',


            'Preview',
            'Media URL',
            'Preview',
            'Media URL',
            'Preview',
            'Media URL',
            'Preview',
            'Media URL',
            'Preview',
            'Media URL',
            'Preview',
            'Media URL',
            'Preview',
            'Media URL',
            'Preview',
            'Media URL',
            'Preview',
            'Media URL',
            'Preview',
            'Media URL',


            'price currency id',
            'price quantity',
            'Price_US_IN',
            'price tax rate',

            'price currency id1',
            'price quantity',
            'Price_US_US1',
            'price tax rate',

            'price currency id',
            'price quantity',
            'Price_US_UAE',
            'price tax rate',

            'price currency id',
            'price quantity',
            'Price value',
            'price tax rate',

            'price currency id',
            'price quantity',
            'Price value',
            'price tax rate',

            'attribute code',
            'attribute type',
            'attribute label',
            'attribute position',
            'attribute status',
            'product list type',

            'attribute code',
            'attribute type',
            'attribute label',
            'attribute position',
            'attribute status',
            'product list type',


            'attribute code',
            'attribute type',
            'attribute label',
            'attribute position',
            'attribute status',
            'product list type',

            'attribute code',
            'attribute type',
            'attribute label',
            'attribute position',
            'attribute status',
            'product list type',

            'attribute code',
            'attribute type',
            'attribute label',
            'attribute position',
            'attribute status',
            'product list type',

            'subproduct code',
            'product list type',
            'subproduct code',
            'product list type',
            'subproduct code',
            'product list type',
            'subproduct code',
            'product list type',
            'subproduct code',
            'product list type',
            'subproduct code',
            'product list type',
            'subproduct code',
            'product list type',
            'subproduct code',
            'product list type',
            'subproduct code',
            'product list type',
            'subproduct code',
            'product list type',



            'property list type',
            'property value',
            'catalog code',
            'catalog code',
            'catalog code',
            'catalog code',

            'catalog list type',
            'catalog list date start',
            'catalog list date end',
            'catalog list config',
            'catalog list position',
            'catalog list status',
            'supplier code',
            'supplier list type',
            'supplier list datestart',
            'supplier list dateend',
            'supplier list config',
            'supplier list position',
            'supplier list status',
            'stock level',
            'stock type',
            'stock dateback',

        ];

        $total_csv = 100000;
        $chunk = 10000;
        $csv_number = $total_csv / $chunk;

        $table_name = table_model_create(country_code: 'us', model: 'Catalog', table_name: 'catalognew');

        $csv_values = [];
        $catalog_csv_writer = '';


        $result = $table_name->select($headers)
            ->join('pricing_uss', 'catalognewuss.asin', '=', 'pricing_uss.asin')
            ->whereIn('catalognewuss.asin', $asin)
            ->get()->toArray();

        if ($this->count == 1) {

            $this->file_path = "Cliqnshop/imported_cat/" . "uploaded_asin_CatalogCliqnshop" . $this->offset . ".csv";
            if (!Storage::exists($this->file_path)) {
                Storage::put($this->file_path, '');
            }
            $catalog_csv_writer = Writer::createFromPath(Storage::path($this->file_path, 'w'));
            $catalog_csv_writer->insertOne($csv_header);
        }


        foreach ($result as $data) {

            $img1 = [
                "Images1" => '',
                "preview1" => '',
                "Images2" => '',
                "preview2" => '',
                "Images3" => '',
                "preview3" => '',
                "Images4" => '',
                "preview4" => '',
                "Images5" => '',
                "preview5" => '',
                "Images6" => '',
                "preview6" => '',
                "Images7" => '',
                "preview7" => '',
                "Images8" => '',
                "preview8" => '',
                "Images9" => '',
                "preview9" => '',
                "Images10" => '',
                "preview10" => '',
            ];
            $imagedata = json_decode($data['images'], true);

            if (isset($imagedata[0]['images'])) {

                foreach ($imagedata[0]['images'] as $counter => $image_data_new) {
                    $counter++;
                    if (array_key_exists("link", $image_data_new)) {

                        if ($img1["Images${counter}"] = $image_data_new['height'] == 75) {

                            $img1["Images${counter}"] = '';
                            $img1["preview${counter}"] = '';
                        } else {
                            $img1["Images${counter}"] = $image_data_new['link'];
                            $img1["preview${counter}"] = $image_data_new['link'];
                        }
                    } else {
                        $img1["Images${counter}"] = null;
                        $img1["preview${counter}"] = null;
                    }
                    if ($counter == 10) {
                        break;
                    }
                }
            } else {
                for ($i = 1; $i <= 5; $i++) {
                    $img1["Images${i}"] = null;
                    $img1["preview${i}"] = null;
                }
            }
            $cid = json_decode($data['browse_classification'], true);
            $cat_code = 'new';
            $cat_code1 = 'all';
            if ($cid == null) {
                $cat_code = 'new';
                $cat_code1 = 'all';
            } else if (isset($cid['classificationId'])) {
                $cat_code = $cid['classificationId'];
                $cat_code1 = null;
            }

            if (isset($data['dimensions'])) {
                $height_unit  = null;
                $height_val  = null;
                $h_type = null;
                $hh = null;
                $hpro_typt = null;
                $dim = json_decode($data['dimensions'], true);
                if (isset($dim[0]['item']['height'])) {
                    $height_unit  = $dim[0]['item']['height']['unit'];
                    $height_val  = $dim[0]['item']['height']['value'];
                    $h_type =  (key($dim[0]['item']));
                    $hh = '1';
                    $hpro_typt = 'default';
                }
                if (isset($dim[0]['item']['length'])) {
                    $length_unit  = $dim[0]['item']['length']['unit'];
                    $length_val  = $dim[0]['item']['length']['value'];
                    $ll = '1';
                    $l_type = 'length';
                    $lpro_typt = 'default';
                } else {
                    $length_unit = null;
                    $length_val = null;
                    $l_type = null;
                    $ll = null;
                    $lpro_typt = null;
                }

                if (isset($dim[0]['item']['width'])) {
                    $width_unit  = $dim[0]['item']['width']['unit'];
                    $width_val  = $dim[0]['item']['width']['value'];
                    $wd_type = 'width';
                    $wd = '1';
                    $wd_pro_typt = 'default';
                } else {
                    $width_unit = null;
                    $width_val  = null;
                    $wd_type = null;
                    $wd = null;
                    $wd_pro_typt = null;
                }
            } else {
                $height_unit  = null;
                $height_val  = null;
                $length_unit = null;
                $length_val = null;
                $weight_unit = null;
                $weight_val = null;
                $width_unit = null;
                $width_val  = null;
            }

            if (isset($data['attributes'])) {

                $desc = json_decode($data['attributes'], true);
                if (isset($desc['bullet_point'])) {

                    $bullet = $desc['bullet_point'];
                    $long_desc = '';
                    foreach ($bullet as $key => $val) {
                        $sh_name = "long";
                        $long_desc .= "<p>" . $val['value'] . "</p>";
                        $sh_name1 = "short";
                        $short_desc1 = ($val['value']);
                    }
                } else {
                    $sh_name = null;
                    $long_desc = null;
                    $sh_name1 = null;
                    $short_desc1 = null;
                }
            }

            if (isset($data['color'])) {
                $color_code = str_replace(' ', '', $data['color']);
                $color_type = 'color';
                $clor_label = $data['color'];
                $color_status = '1';
                $color_list_type = 'default';
            } else {
                $color_code = null;
                $color_type = null;
                $clor_label = null;
                $color_status = null;
                $color_list_type = null;
            }



            $brand_place = str_replace(' ', '', $data['brand']);

            $csv_values[] = [
                'item code' => $data['asin'],
                'item label' => $data['item_name'],
                'item type' =>  'default',
                'item status' => '1',
                'text type' => $sh_name1,
                'text content' => $short_desc1,
                'text type0' => $sh_name,
                'text content0' => ($long_desc),
                ...$img1,
                'price currency id' => 'INR',
                'price quantity' => '1',
                'Price_US_IN' => $data['usa_to_in_b2c'],
                'price tax rate' => '19',

                'price currency id1' => 'USD',
                'price quantity1' => '1',
                'Price_US_US1' => $data['us_price'],
                'price tax rate1' => '19',

                'price currency id2' => 'AED',
                'price quantity2' => '1',
                'Price_US_UAE2' => $data['usa_to_uae'],
                'price tax rate2' => '19',

                'price currency id3' => null,
                'price quantity3' => null,
                'Price value3' => null,
                'price tax rate3' => null,

                'price currency id4' => null,
                'price quantity4' => null,
                'Price value4' => null,
                'price tax rate4' => null,

                'attribute code0' => $color_code,
                'attribute type0' => $color_type,
                'attribute label0' => $clor_label,
                'attribute position0' => null,
                'attribute status0' => $color_status,
                'product list type0' => $color_list_type,


                'attribute code1' => $length_val,
                'attribute type1' => $l_type,
                'attribute label' =>  $length_val . ' ' . $length_unit,
                'attribute position1' => null,
                'attribute status1' => $ll,
                'product list type1' =>   $lpro_typt,

                'attribute code2' => $width_val,
                'attribute type2' => $wd_type,
                'attribute label2' => $width_val . ' ' . $width_unit,
                'attribute position2' => null,
                'attribute status2' => $wd,
                'product list type2' => $wd_pro_typt,

                'attribute code3' => null,
                'attribute type3' => null,
                'attribute label3' => null,
                'attribute position3' => null,
                'attribute status3' => null,
                'product list type3' =>  null,

                'attribute code4' => null,
                'attribute type4' => null,
                'attribute label4' => null,
                'attribute position4' => null,
                'attribute status4' => null,
                'product list type4' => null,

                'subproduct code0' => null,
                'product list typea' => null,
                'subproduct code1' => null,
                'product list typeb' => null,
                'subproduct code2' => null,
                'product list typec' => null,
                'subproduct code3' => null,
                'product list typed' => null,
                'subproduct code4' => null,
                'product list typee' => null,
                'subproduct code5' => null,
                'product list typef' => null,
                'subproduct code6' => null,
                'product list typeg' => null,
                'subproduct code7' => null,
                'product list typeh' => null,
                'subproduct code8' => null,
                'product list typei' => null,
                'subproduct code9' => null,
                'product list typej' => null,


                'property list type' => null,
                'property value' => null,
                'catalog code' =>  $cat_code,
                'catalog code0' =>   $cat_code1,
                'catalog code1' => null,
                'catalog code2' => null,

                'catalog list type' => null,
                'catalog list date start' => null,
                'catalog list date end' => null,
                'catalog list config' => null,
                'catalog list position' => null,
                'catalog list status' => '1',
                'supplier code' =>   substr(strtolower($brand_place), 0, 10),
                'supplier list type' => null,
                'supplier list datestart' => null,
                'supplier list dateend' => null,
                'supplier list config' => null,
                'supplier list position' => null,
                'supplier list status' => '1',
                'stock level' => '500',
                'stock type' => 'default',
                'stock dateback' => null,

            ];

            $brand_place_second = str_replace(' ', '', $data['brand']);
            $second_csv_headers = [
                'Code',
                'Label',
                'Status',
                'Text Language',
                'Text Type',
                'Text Content',
                'Media Type',
                'Media Url',
                'Address Language Id',
                'Country Code',
                'City'
            ];
            $Status2 = null;
            $txt_lng2 = null;
            if (!empty($data['brand'])) {
                $Status2 = "1";
                $txt_lng2 = "default";
            }

            $second_csv_values[] = [
                'code' => substr(strtolower($brand_place_second), 0, 10),
                'Label' =>  strtoupper($data['brand']),
                'Status2' =>  $Status2,
                'Text Language2' => $txt_lng2,
                'Text Type' => null,
                'Text Content' => null,
                'Media Type' => null,
                'Media Url' => null,
                'Address Language Id' => null,
                'Country Code' => null,
                'City' => null,
            ];

            $exportFilePath = "Cliqnshop/imported_cat/" . "uploaded_asin_brandCliqnshop" . $this->offset . ".csv";
            if (!Storage::exists($exportFilePath)) {
                Storage::put($exportFilePath, '');
            }
            $brand_csv_writer = Writer::createFromPath(Storage::path($exportFilePath), "w");
            $brand_csv_writer->insertOne($second_csv_headers);


            $brand_csv_writer->insertAll($second_csv_values);
        } // end of result loop  

        $catalog_csv_writer->insertAll($csv_values);

        if ($csv_number == $this->count) {
            ++$this->offset;

            $this->count = 1;
        } else {
            ++$this->count;
        }
    }
}
