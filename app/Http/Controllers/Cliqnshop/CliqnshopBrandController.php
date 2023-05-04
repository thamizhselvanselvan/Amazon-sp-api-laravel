<?php

namespace App\Http\Controllers\Cliqnshop;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class CliqnshopBrandController extends Controller
{
    

    public function ban_brands_index(Request $request)
    {
        $site_id = $request->site_id;
        $url = "/cliqnshop/brand/ban";

        if ($site_id != null) {
            $url = "/cliqnshop/brand/ban/".$site_id;

            $data = DB::connection('cliqnshop')->table('cns_ban_brand')->where('site_id', $site_id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {

            $data = DB::connection('cliqnshop')->table('cns_ban_brand')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        if ($request->ajax()) {         
            return Datatables::of($data)
            ->addColumn('action', function ($row) {
                return "<div class='d-flex'><a href='javascript:void(0)' name='id' id='edit' value ='$row->id' data-siteid='$row->site_id' data-brand='$row->brand' class='edit btn btn-success btn-sm'><i class='fas fa-edit'></i>Edit</a>"
                ."<div class='d-flex'><a href='javascript:void(0)' name='id' id='delete' value ='$row->id'  class='delete btn btn-danger btn-sm ml-2'><i class='far fa-trash-alt'></i>Remove</a></div>";
              })
              ->addColumn('site', function ($data) {
                $s_code = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid',$data->site_id)->pluck('code')->ToArray();
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
              ->rawColumns(['action','site'])
              ->make(true);
                    
        }
        $sites = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();

        return view('Cliqnshop.brand.ban_brands_index', compact('sites','url'));
    
    }

    public function store_ban_brands(Request $request)
    {
        $brand = $request->validate([
            'site' => 'required',
            'brand' => 'required',
        ]);
        DB::connection('cliqnshop')->table('cns_ban_brand')->insert([
            'site_id' => $brand['site'],
            'brand' => $brand['brand'],
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        return response()->json(['Successfully']);
    }

    public function update_brand(Request $request)
    {
        $brand = $request->validate([
            'id' => 'required',
            'site' => 'required',
            'brand' => 'required',
        ]);
        DB::connection('cliqnshop')->table('cns_ban_brand')
        ->where('id',$brand['id'],)
        ->update([
            'site_id' => $brand['site'],
            'brand' => $brand['brand'],
            'updated_at' => now(),
        ]);
        return response()->json(['Successfully']);
    }

    public function delete_brand(Request $request)
    {
        $brand = $request->validate([
            'id' => 'required',
        ]);
        DB::connection('cliqnshop')->table('cns_ban_brand')
        ->where('id',$brand['id'],)
        ->delete();
        return response()->json(['Successfully']);
    }    
}
