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
    // private $shelves_details;
    private $source_details;
    private $ship_id;
    private $currency_details;
    private $tag_details;

    public function __construct()
    {
        $this->warehouse_details = Warehouse::get(['id', 'name'])->toArray();
        // $this->shelves_details = Shelve::get(['id', 'warehouse', 'rack_id', 'shelve_id', 'name'])->toArray();
        $this->source_details = Vendor::query()
            ->where('type', 'Source')
            ->get(['id', 'name'])
            ->toArray();

        $this->currency_details = Currency::get()->toArray();
        $this->tag_details = Tag::get()->toArray();

        $this->ship_id = $this->generateShipmentId();
    }

    public function index($file_path)
    {
        $warehouse_array = [];
        foreach ($this->warehouse_details as $value) {
            $warehouse_array[$value['id']] = trim($value['name']);
        }

        $source_array = [];
        foreach ($this->source_details as $value) {
            $source_array[trim($value['name'])] = trim($value['id']);
        }

        // $rack_shelve_details = [];
        // foreach ($this->shelves_details as $value) {
        //     if (array_key_exists($value['warehouse'], $warehouse_array)) {
        //         $rack_shelve_details[$warehouse_array[$value['warehouse']]][$value['rack_id']][$value['shelve_id']] = $value['name'];
        //     }
        // }

        $currency_array = [];
        foreach ($this->currency_details as $value) {
            $currency_array[$value['code']] = $value['id'];
        }

        $tag_array = [];
        foreach ($this->tag_details as $value) {
            $tag_array[$value['name']] = $value['id'];
        }

        $records = CSV_Reader($file_path);

        $total_item_count = 0;
        $source_id = [];
        $data = [];
        $err = [];
        $multi_source_id = [];
        $warehouse_id = '';
        $currency_code = '';

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
                $ss_id = trim($value['Source ID']);
                $rack_id = trim($value['Rack ID']);
                $shelves_id = trim($value['Shelve ID']);
                // $bin_id = $value['Bin ID'];
                $tag = $value['Tag'];

            //    if (isset($rack_shelve_details[$warehouse_name][$rack_id][$shelves_id])&& isset($warehouse_array[$warehouse_name]) && isset($source_array[$source])) {
                  $data['inward_date'] = $inward_date;
                  $data['asin'] = $asin;
                  $data['item_name'] = $item_name;
                  $data['qty'] = $qty;
                  $data['pro_price'] = $pro_price;
                  $data['sales_price'] = $sales_price;
                  $data['currency'] = $currency;
                  $data['warehouse_id'] = array_search($warehouse_name, $warehouse_array);
                  $data['source'] = $source_array[$source];
                  $data['source_id'] = $ss_id;
                  $data['rack_id'] = $rack_id;
                  $data['shelves_id'] = $shelves_id;
                  $data['tag'] = array_key_exists($tag, $tag_array) ? $tag_array[$tag] : '';
                  $source_id[] = $source_array[$source];

                  $multi_source_id[] = $source;

                  $this->InventoryDataInsert($data);
                  $total_item_count++;
                  $warehouse_id = $data['warehouse_id'];
                  $currency_code = $currency;
               /*  } else {
                    Log::alert('no name for' . $asin);
                   
                    //Send notification for invalid entries
                 } */
            } catch (Exception $e) {
                $err[] = $asin;
                Log::debug(($e));
            }
        }

        try {

            $multi_source_id = (json_encode(array_unique($multi_source_id)));
            $this->InventroyShipmentInwardDataInsert($warehouse_id, $currency_code, $total_item_count, $multi_source_id);
        } catch (Exception $e) {
            Log::debug(('INV Multi Source'.$e));

            $err[] = $asin;
        }

        if (count($err) > 0) {
            return $err;
        } else {
            return true;
        }
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
            "ss_id" => $data['source_id'],
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
            "ss_id" => $data['source_id'],
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

    public function InventroyShipmentInwardDataInsert($warehouse_id, $currency_code, $total_item_count, $multi_source_id)
    {
        if ($warehouse_id && $currency_code) {
            Shipment_Inward::insert([
                "warehouse_id" => $warehouse_id,
                "source_id" => $multi_source_id, //source id multiple if multi source is available
                "ship_id" =>  $this->ship_id,
                "currency" => $currency_code,
                "shipment_count" => $total_item_count,
                "inwarded_at" => now(),
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
    }
}
