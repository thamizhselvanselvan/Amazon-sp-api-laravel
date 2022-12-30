<?php

namespace App\Services\Inventory;

use Exception;
use League\Csv\Reader;
use App\Models\Currency;
use App\Models\Inventory\Tag;
use App\Models\Inventory\Shelve;
use App\Models\Inventory\Vendor;
use App\Models\Inventory\Inventory;
use App\Models\Inventory\Warehouse;
use Illuminate\Support\Facades\Log;
use App\Models\Inventory\Shipment_Inward;
use App\Models\Inventory\Shipment_Inward_Details;


class InventoryCsvImport
{
    private $warehouse_details;
    private $shelves_details;
    private $source_details;
    private $ship_id;
    private $currency_details;
    private $tag_details;

    public function __construct()
    {
        $this->warehouse_details = Warehouse::get(['id', 'name'])->toArray();
        $this->shelves_details = Shelve::get(['id', 'warehouse', 'rack_id', 'shelve_id', 'name'])->toArray();
        $this->source_details = Vendor::query()
            ->where('type', 'Source')
            ->get(['id', 'name'])
            ->toArray();

        $this->currency_details = Currency::get()->toArray();
        $this->tag_details = Tag::get()->toArray();

        $this->ship_id = $this->generateShipmentId();
    }

    public function index()
    {
        $warehouse_array = [];
        foreach ($this->warehouse_details as $value) {
            $warehouse_array[$value['id']] = $value['name'];
        }

        $source_array = [];
        foreach ($this->source_details as $value) {
            $source_array[$value['name']] = $value['id'];
        }

        $rack_shelve_details = [];
        foreach ($this->shelves_details as $value) {
            if (array_key_exists($value['warehouse'], $warehouse_array)) {
                $rack_shelve_details[$warehouse_array[$value['warehouse']]][$value['rack_id']][$value['shelve_id']] = $value['name'];
            }
        }

        $currency_array = [];
        foreach ($this->currency_details as $value) {
            $currency_array[$value['code']] = $value['id'];
        }

        $tag_array = [];
        foreach ($this->tag_details as $value) {
            $tag_array[$value['name']] = $value['id'];
        }

        // $csv_data = CSV_Reader('D:/Inventory.csv');
        $reader = Reader::createFromPath('D:/Inventory.csv', 'r');
        $reader->setHeaderOffset(0);

        $records = ($reader->getRecords());

        $total_item_count = 0;
        $source_id = [];
        $data = [];
        $multi_source_id = [];
        foreach ($records as $value) {

            try {
                $inward_date = $value['Inward Date'];
                $asin = $value['ASIN'];
                $item_name  = html_entity_decode($value['Item Name']);
                $qty = $value['Quantity'];
                $pro_price = $value['Procurement Price'];
                $sales_price = $value['Sales Price'];
                $currency = $currency_array[$value['Currency']];
                $warehouse_name = trim($value['Warehouse Name']);
                $source = trim($value['Source']);
                $rack_id = trim($value['Rack ID']);
                $shelves_id = trim($value['Shelve ID']);
                $bin_id = $value['Bin ID'];
                $tag = $value['Tag'];

                if (isset($rack_shelve_details[$warehouse_name][$rack_id][$shelves_id]) && isset($source_array[$source])) {

                    $data['inward_date'] = $inward_date;
                    $data['asin'] = $asin;
                    $data['item_name'] = $item_name;
                    $data['qty'] = $qty;
                    $data['pro_price'] = $pro_price;
                    $data['sales_price'] = $sales_price;
                    $data['currency'] = $currency;
                    $data['warehouse_id'] = array_search($warehouse_name, $warehouse_array);
                    $data['source'] = $source_array[$source];
                    $data['rack_id'] = $rack_id;
                    $data['shelves_id'] = $shelves_id;
                    $data['tag'] = $tag_array[$tag];
                    $source_id[] = $source_array[$source];

                    $multi_source_id[] = $source;

                    $this->InventoryDataInsert($data);
                    $total_item_count++;
                } else {

                    //Send notification for invalid entries
                }
            } catch (Exception $e) {

                Log::info($e);
                //throw error for exception case
            }
        }
        $multi_source_id = (json_encode(array_unique($multi_source_id)));
        $this->InventroyShipmentInwardDataInsert($data, $total_item_count, $multi_source_id);
    }

    public function generateShipmentId()
    {
        start:
        $uniq = random_int(1000, 99999);
        $ship_id = 'INW' . $uniq;

        $val = Shipment_Inward::query()
            ->select(('ship_id'))
            ->where('ship_id', $ship_id)
            ->first();

        if ($val) {
            goto start;
        }

        return $ship_id;
    }

    public function InventoryDataInsert($data)
    {
        Shipment_Inward_Details::create([
            "warehouse_id" => $data['warehouse_id'],
            "source_id" =>  $data['source'],
            "ship_id" => $this->ship_id,
            "currency" => $data['currency'],
            "asin" => $data['asin'],
            "item_name" => $data['item_name'],
            "tag" => $data['tag'],
            "price" => $data['sales_price'],
            "procurement_price" => $data['pro_price'],
            "quantity" => $data['qty'],
            "inwarded_at" => now()
        ]);

        Inventory::create([
            "warehouse_id" => $data['warehouse_id'],
            "source_id" =>  $data['source'],
            "ship_id" => $this->ship_id,
            "asin" =>  $data['asin'],
            "price" => $data['sales_price'],
            "procurement_price" => $data['pro_price'],
            "item_name" => $data['item_name'],
            "tag" => $data['tag'],
            "quantity" => $data['qty'],
            "balance_quantity" => $data['qty'],
            "bin" =>  $data['shelves_id'], //shelves id
            "inwarded_at" => now()
        ]);
    }

    public function InventroyShipmentInwardDataInsert($data, $total_item_count, $multi_source_id)
    {
        Shipment_Inward::insert([
            "warehouse_id" => $data['warehouse_id'],
            "source_id" => $multi_source_id, //source id multiple if multi source is available
            "ship_id" =>  $this->ship_id,
            "currency" => $data['currency'],
            "shipment_count" => $total_item_count,
            "inwarded_at" => now(),
            "created_at" => now(),
            "updated_at" => now(),
        ]);
    }
}
