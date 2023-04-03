<?php

namespace App\Http\Controllers\Cliqnshop;

use PDO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

            $product = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $site_id)->pluck('id')->ToArray();

            $brand = DB::connection('cliqnshop')->table('mshop_product_list')->where('siteid', $site_id)->whereIn('parentid', $product)->where('domain', 'supplier')->pluck('parentid')->ToArray();

            $data = DB::connection('cliqnshop')->table('mshop_product')->where('siteid', $site_id)->wherenotIn('id', $brand)->orderBy('ctime', 'desc')->get();

        } else {

            DB::connection('cliqnshop')->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

            $product = DB::connection('cliqnshop')->table('mshop_product')->pluck('id')->ToArray();

            $brand = DB::connection('cliqnshop')->table('mshop_product_list')->whereIn('parentid', $product)->where('domain', 'supplier')->pluck('parentid')->ToArray();

            $data = DB::connection('cliqnshop')->table('mshop_product')->wherenotIn('id', $brand)->orderBy('ctime', 'desc')->get();

            DB::connection('cliqnshop')->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }

        if ($request->ajax()) {
            return Datatables::of($data)
                ->make(true);
        }

        $sites = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.brand.missing_brands_index', compact('sites', 'url'));
    }
}
