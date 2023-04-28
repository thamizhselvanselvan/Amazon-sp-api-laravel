<?php

namespace App\Http\Controllers\Cliqnshop;

use PDO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class MissingCatalogDetailsController extends Controller
{
    public function brand_missing_index(Request $request)
    {
        $site_id = $request->site_id;
        $url = "/cliqnshop/brand/missing";

        if ($site_id != null) {
            $url = "/cliqnshop/brand/missing/" . $site_id;

            DB::connection('cliqnshop')->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

            $product = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $site_id)->pluck('id')->ToArray();

            $brand = DB::connection('cliqnshop')->table('mshop_product_list')->where('siteid', $site_id)->whereIn('parentid', $product)->where('domain', 'supplier')->pluck('parentid')->ToArray();

            $data = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $site_id)->wherenotIn('id', $brand)->orderBy('ctime', 'desc')->get();

            DB::connection('cliqnshop')->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        } else {

            DB::connection('cliqnshop')->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

            $product = DB::connection('cliqnshop')->table('mshop_product')->pluck('id')->ToArray();

            $brand = DB::connection('cliqnshop')->table('mshop_product_list')->whereIn('parentid', $product)->where('domain', 'supplier')->pluck('parentid')->ToArray();

            $data = DB::connection('cliqnshop')->table('mshop_product')->wherenotIn('id', $brand)->orderBy('ctime', 'desc')->get();

            DB::connection('cliqnshop')->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }

        if ($request->ajax()) {
            return Datatables::of($data)
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return "<center><p class='status_checks statusButton btn-success w-75'> Enable </p></center>";
                    } else {
                        return "<center><p class='status_checks statusButton btn-danger w-75'> Disable </p></center>";
                    }
                })
                ->addColumn('site', function ($data) {
                    $s_code = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid',$data->siteid)->pluck('code')->ToArray();
                    if ($s_code[0] == 'uae')
                    {
                        return '<p class = "text-danger">UAE</p>';
                    }
                    elseif ($s_code[0] == 'in') {
                        return '<p class = "text-success">India</p>';
                    }
                    else {
                        return $s_code[0];
                    }
                })
                ->rawColumns(['status','site'])
                ->make(true);
        }

        $sites = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.brand.missing_brands_index', compact('sites', 'url'));
    }

    public function category_missing_index(Request $request)
    {
        $site_id = $request->site_id;
        $url = "/cliqnshop/category/missing";

        if ($site_id != null) {
            $url = "/cliqnshop/category/missing/" . $site_id;

            $cat = DB::connection('cliqnshop')->table('mshop_catalog')->where('siteid', $site_id)->where('code','demo-new')->pluck('id')->ToArray();

            
            $product_list = DB::connection('cliqnshop')->table('mshop_product_list')->where('siteid', $site_id)->whereIn('refid',$cat)->where('domain', 'catalog')->pluck('parentid')->ToArray();
    
            DB::connection('cliqnshop')->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

            $data = $data = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $site_id)->whereIn('id', $product_list)->orderBy('ctime', 'desc')->get();

            DB::connection('cliqnshop')->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
        } else {

        $cat = DB::connection('cliqnshop')->table('mshop_catalog')->where('code','demo-new')->pluck('id')->ToArray();

        $product_list = DB::connection('cliqnshop')->table('mshop_product_list')->whereIn('refid',$cat)->where('domain', 'catalog')->pluck('parentid')->ToArray();

        DB::connection('cliqnshop')->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

        $data = $data = DB::connection('cliqnshop')->table('mshop_product')->whereIn('id', $product_list)->orderBy('ctime', 'desc')->get();
        
        DB::connection('cliqnshop')->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        }

        if ($request->ajax()) {
            return Datatables::of($data)
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return "<center><p class='status_checks statusButton btn-success w-75'> Enable </p></center>";
                    } else {
                        return "<center><p class='status_checks statusButton btn-danger w-75'> Disable </p></center>";
                    }
                })
                ->addColumn('site', function ($data) {
                    $s_code = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid',$data->siteid)->pluck('code')->ToArray();
                    if ($s_code[0] == 'uae')
                    {
                        return '<p class = "text-danger">UAE</p>';
                    }
                    elseif ($s_code[0] == 'in') {
                        return '<p class = "text-success">India</p>';
                    }
                    else {
                        return $s_code[0];
                    }
                })
                ->rawColumns(['status','site'])
                ->make(true);
        }

        $sites = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.category.missing_categories_index', compact('sites', 'url'));
    }
}
