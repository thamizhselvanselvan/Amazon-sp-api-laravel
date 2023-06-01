<?php

namespace App\Http\Controllers\Cliqnshop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CliqnshopVerificationController extends Controller
{
    public function verify_asin_index(Request $request)
    {
        if ($request->ajax()) {

            if ($request->has("site_id") && $request->has("editor") && $request->editor !== "search")
            {
                $data = DB::connection('cliqnshop')->table('mshop_product')
                    ->where(['siteid' => $request->site_id])
                    ->orderBy('mtime', 'desc');
            }
            elseif ($request->has("site_id") && $request->has("editor") && $request->editor !== "all")
            {
                $data = DB::connection('cliqnshop')->table('mshop_product')
                    ->where(['siteid' => $request->site_id])
                    ->whereIn('editor', ['cns_search_from_in', 'cns_search_from_uae'])
                    ->orderBy('mtime', 'desc');
            }
            elseif ($request->has("site_id")) {
                //  \Illuminate\Support\Facades\Log::alert('validation failed');

                $data = DB::connection('cliqnshop')->table('mshop_product')
                    ->where(['siteid' => $request->site_id])
                    ->whereIn('editor', ['cns_search_from_in', 'cns_search_from_uae'])
                    ->orderBy('mtime', 'desc');
            }
            elseif ($request->has("editor") && $request->editor !== "all") {
                $data = DB::connection('cliqnshop')->table('mshop_product')
                    ->whereIn('editor', ['cns_search_from_in', 'cns_search_from_uae'])
                    ->orderBy('mtime', 'desc');
            }
            elseif ($request->has("editor") && $request->editor !== "search") {
                $data = DB::connection('cliqnshop')->table('mshop_product')
                    ->orderBy('mtime', 'desc');
            }
            else {
                $data = DB::connection('cliqnshop')->table('mshop_product')
                    ->whereIn('editor', ['cns_search_from_in', 'cns_search_from_uae'])
                    ->orderBy('mtime', 'desc');
            }

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('action', function ($data) {
                    $pid = $data->id;
                    $asin = $data->asin;
                    $code = $data->code;
                    $site = $data->siteid;
                    $label = $data->label;
                    $status = $data->status;
                    $type = $data->type;
                    $actionbtn = '';
                    if ($status == 0) {
                        $actionbtn = "<div class='d-flex'><a href=" . route('cliqnshop.verify.asin.approve', ['pid' => $pid, 'asin' => $asin, 'site' => $site]) . " id='offers2' value ='$pid' data-label='$label' data-type='$type' class='approveAsin btn  bg-gradient-success btn-xs mr-2'>Approve</a>";
                    }
                    $actionbtn .= "<div class='d-flex'><a href=" . route('cliqnshop.verify.asin.destroy', ['pid' => $pid, 'code' => $code, 'asin' => $asin, 'site' => $site]) . " id='offers1' value ='$pid' data-label='$label' data-type='$type' class='deleteAsin btn  bg-gradient-danger btn-xs'>Remove</a>";
                    return $actionbtn;
                })
                ->addColumn('site', function ($data) {
                    $s_code = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid', $data->siteid)->pluck('code')->ToArray();
                    if ($s_code[0] == 'uae') {
                        return '<center><p class = "text-danger">UAE</p></center>';
                    }
                    elseif ($s_code[0] == 'in') {
                        return '<center><p class = "text-success">India</p></center>';
                    }
                    else {
                        return $s_code[0];
                    }
                })
                ->addColumn('category', function ($data) {
                    $category_check = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $data->id, 'siteid' => $data->siteid])->where('domain', 'catalog')->pluck('refid')->ToArray();
                    $check_category = DB::connection('cliqnshop')->table('mshop_catalog')->whereIn('id', $category_check)->where(['code' => 'demo-new', 'siteid' => $data->siteid])->get()->ToArray();
                    if ($check_category == []) {
                        return '<span data-toggle="tooltip" data-placement="top" title="Category Found" style="font-size:20px;">&#9989;</span>';
                    } else {
                        return '<span data-toggle="tooltip" data-placement="top" title="Category Not Found" style="font-size:20px;">&#10060;</span>';
                    }
                })
                ->addColumn('price', function ($data) {
                    $check_price = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $data->id, 'siteid' => $data->siteid])->where('domain', 'price')->pluck('refid')->ToArray();
                if(isset($check_price[0]))
                {
                    $price_check = DB::connection('cliqnshop')->table('mshop_price')->where(['id' => $check_price[0], 'siteid' => $data->siteid, 'value' => 0.00])->get()->ToArray();
                    if ($price_check == []) {
                        return '<span data-toggle="tooltip" data-placement="top" title="Price Found" style="font-size:20px;">&#9989;</span>';
                    }
                    else {
                        return '<span data-toggle="tooltip" data-placement="top" title="Price Not Found" style="font-size:20px;">&#10060;</span>';
                    }
                }
                     else {
                        return '<span data-toggle="tooltip" data-placement="top" title="Price Not Found" style="font-size:20px;">&#10060;</span>';
                    }
                })
                ->addColumn('image', function ($data) {
                    $image_check = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $data->id, 'siteid' => $data->siteid])->where('domain', 'media')->get()->ToArray();
                    if ($image_check !== []) {
                        return '<span data-toggle="tooltip" data-placement="top" title="Image Found" style="font-size:20px;">&#9989;</span>';
                    } else {
                        return '<span data-toggle="tooltip" data-placement="top" title="Image Not Found" style="font-size:20px;">&#10060;</span>';
                    }
                })
                ->addColumn('brand', function ($data) {
                    $brand_check = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $data->id, 'siteid' => $data->siteid])->where('domain', 'supplier')->get()->ToArray();
                    if ($brand_check !== []) {
                        return '<span data-toggle="tooltip" data-placement="top" title="Brand Found" style="font-size:20px;">&#9989;</span>';
                    } else {
                        return '<span data-toggle="tooltip" data-placement="top" title="Brand Not Found" style="font-size:20px;">&#10060;</span>';
                    }
                })
                ->addColumn('description', function ($data) {
                    $description_check = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $data->id, 'siteid' => $data->siteid])->where('domain', 'text')->pluck('refid')->ToArray();

                  if(isset($description_check[0]))

                  {
                    $check_description = DB::connection('cliqnshop')->table('mshop_text')->where(['id' => $description_check[0], 'type' => 'long'])->whereNotIn('content', ['', ' '])->get()->ToArray();
                    if ($check_description !== []) {
                        return '<span data-toggle="tooltip" data-placement="top" title="Description Found" style="font-size:20px;">&#9989;</span>';
                    }
                    else {
                        return '<span data-toggle="tooltip" data-placement="top" title="Description Not Found" style="font-size:20px;">&#10060;</span>';
                    }
                  }
                     else {
                        return '<span data-toggle="tooltip" data-placement="top" title="Description Not Found" style="font-size:20px;">&#10060;</span>';
                    }
                })
                ->addColumn('colour', function ($data) {
                    $colour_check = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $data->id, 'siteid' => $data->siteid])->where('domain', 'attribute')->pluck('refid')->ToArray();
                    $check_colour = DB::connection('cliqnshop')->table('mshop_attribute')->whereIn('id', $colour_check)->where(['type' => 'color', 'siteid' => $data->siteid])->get()->ToArray();
                    if ($check_colour !== []) {
                        return '<span data-toggle="tooltip" data-placement="top" title="Colour Found" style="font-size:20px;">&#9989;</span>';
                    } else {
                        return '<span data-toggle="tooltip" data-placement="top" title="Colour Not Found" style="font-size:20px;">&#10060;</span>';
                    }
                })
                ->addColumn('length', function ($data) {
                    $length_check = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $data->id, 'siteid' => $data->siteid])->where('domain', 'attribute')->pluck('refid')->ToArray();
                    $check_length = DB::connection('cliqnshop')->table('mshop_attribute')->whereIn('id', $length_check)->where(['type' => 'length', 'siteid' => $data->siteid])->get()->ToArray();
                    if ($check_length !== []) {
                        return '<span data-toggle="tooltip" data-placement="top" title="Length Found" style="font-size:20px;">&#9989;</span>';
                    } else {
                        return '<span data-toggle="tooltip" data-placement="top" title="Length Not Found" style="font-size:20px;">&#10060;</span>';
                    }
                })
                ->addColumn('width', function ($data) {
                    $width_check = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $data->id, 'siteid' => $data->siteid])->where('domain', 'attribute')->pluck('refid')->ToArray();
                    $check_width = DB::connection('cliqnshop')->table('mshop_attribute')->whereIn('id', $width_check)->where(['type' => 'width', 'siteid' => $data->siteid])->get()->ToArray();
                    if ($check_width !== []) {
                        return '<span data-toggle="tooltip" data-placement="top" title="Width Found" style="font-size:20px;">&#9989;</span>';
                    } else {
                        return '<span data-toggle="tooltip" data-placement="top" title="Width Not Found" style="font-size:20px;">&#10060;</span>';
                    }
                })
                ->addColumn('generic_keywords', function ($data) {
                    $generic_keywords = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $data->id, 'siteid' => $data->siteid])->where('domain', 'keyword')->pluck('refid')->ToArray();

                    $check_generic_keywords = DB::connection('cliqnshop')->table('mshop_keyword')->whereIn('id', $generic_keywords)->whereNotIn('keyword', ['', ' '])->get()->ToArray();
                    if ($check_generic_keywords !== []) {
                        return '<span data-toggle="tooltip" data-placement="top" title="Generic Keywords Found" style="font-size:20px;">&#9989;</span>';
                    } else {
                        return '<span data-toggle="tooltip" data-placement="top" title="Generic Keywords Not Found" style="font-size:20px;">&#10060;</span>';
                    }
                })
                ->addColumn('status', function ($data) {
                    $status = $data->status;
                    if ($status == 0) {
                        return '<center><p class = "text-danger">Disable</p></center>';
                    } else {
                        return '<center><p class = "text-success">Enable</p></center>';
                    }
                })
                ->rawColumns(['action', 'site', 'category', 'price', 'image', 'brand', 'description', 'colour', 'length', 'width', 'generic_keywords', 'status'])
                ->make(true);
        }

        $data['sites'] = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.verify.asin_verify', $data);

    }
    public function verify_asin_destroy(Request $request)
    {
        $this->validate($request,
            [
                'pid' => ['required'],
                'code' => ['required'],
                'site' => ['required'],
                'asin' => ['required'],
            ]
        );

        $pid = $request->pid;
        $code = $request->code;
        $site = $request->site;
        $asin = $request->asin;

        $variant_lists = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site])->pluck('refid')->ToArray();

        if ($variant_lists !== [])
        {
          $Asins = DB::connection('cliqnshop')->table('mshop_product')->whereIn('id', $variant_lists,)->where('siteid', $site)->pluck('asin')->ToArray();

          foreach($Asins as $Asin)
          {
            DB::connection('cliqnshop')->table('cns_ban_asin')->insert(
                [
                    'site_id' => $site,
                    'asin' => $Asin,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);    
          }
        }

        DB::connection('cliqnshop')->table('cns_ban_asin')->insert(
            [
                'site_id' => $site,
                'asin' => $asin,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        commandExecFunc("mosh:remove_exported_asin ${site} ${pid}");

        if ($Asins !== [])
        {
            return back()->with('warning', $asin . ' ' . implode(' ',$Asins).' '. 'added to removable  list command ! and added to permanent ban');
        }
        return back()->with('warning', $asin . ' ' . 'added to removable  list command ! and added to permanent ban');

    }

    public function verify_asin_approve(Request $request)
    {
        $this->validate($request,
            [
                'pid' => ['required'],
                'asin' => ['required'],
                'site' => ['required'],
            ]
        );

        $pid = $request->pid;
        $asin = $request->asin;
        $site = $request->site;

        $check_price = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site])->where('domain', 'price')->pluck('refid')->ToArray();

        if ($check_price == []) {
            return back()->with('error', "Price Not Found! You can't approve" . ' ' . $asin);
        }

        $price_check = DB::connection('cliqnshop')->table('mshop_price')->where(['id' => $check_price[0], 'siteid' => $site, 'value' => 0.00])->get()->ToArray();

        $check_image = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site])->where('domain', 'media')->get()->ToArray();

        if ($price_check !== []) {
            return back()->with('error', "Price Not Found! You can't approve" . ' ' . $asin);
        }

        if ($check_image == []) {
            return back()->with('error', "Image Not Found! You can't approve" . ' ' . $asin);
        }

        if ($price_check == [] && $check_image !== []) {
            DB::connection('cliqnshop')->table('mshop_product')->where(['id' => $pid, 'siteid' => $site])->update(['status' => 1, 'ctime' => now(), 'mtime' => now()]);
            DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site])->update(['status' => 1, 'mtime' => now()]);

            indexrebuild($pid, $site);

            $variant_lists = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site, 'domain' => 'product' ])->pluck('refid')->ToArray();

            if ($variant_lists !== [])
            {
            foreach ($variant_lists as $variant_list)
            {
                DB::connection('cliqnshop')->table('mshop_product')->where(['id' => $variant_list, 'siteid' => $site])->update(['status' => 1, 'ctime' => now(), 'mtime' => now()]);
                DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $variant_list, 'siteid' => $site])->update(['status' => 1, 'mtime' => now()]);

                indexrebuild($variant_list, $site);
            }
            }
            if ($variant_lists !== [])
            {
                $variants = DB::connection('cliqnshop')->table('mshop_product')->whereIn('id', $variant_lists,)->where('siteid', $site)->pluck('asin')->ToArray();
                
                return back()->with('success', 'Approved' . ' ' . $asin . ' '. implode(' ',$variants));
            }
            return back()->with('success', 'Approved' . ' ' . $asin);
        }
    }

}
