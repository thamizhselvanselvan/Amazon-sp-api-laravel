<?php

namespace App\Http\Controllers\Cliqnshop;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class CliqnshopKeywordController extends Controller
{
    public function keyword_search_log_index(Request $request)
    {

        $data = DB::connection('cliqnshop')->table('cns_search_log')
            ->orderBy('created_at', 'desc')
            ->get();

        // dd($data);

        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()

                ->editColumn('created_at', function ($data) {
                    return $diw = \Carbon\Carbon::parse($data->created_at)->diffForHumans();

                })

                ->make(true);
        }

        return view('Cliqnshop.keywordsearch.keyword_search_index');
    }

    public function keyword_search_log_remove(Request $request)
    {

        switch ($request->select_timeline) {
            case ('l-1-h'):
                $del_duration = Carbon::now()->subHours(1);
                break;

            case ('l-24-h'):
                $del_duration = Carbon::now()->subHours(24);
                break;

            case ('l-7-d'):
                $del_duration = Carbon::now()->subDays(7);
                break;

            case ('l-4-w'):
                $del_duration = Carbon::now()->subWeeks(4);
                break;

            case ('all-time'):
                $del_duration = '1-1-1';
                break;
            default:
                return back()->with('error', 'Something went wrong!');

        }

        $res = DB::connection('cliqnshop')->table('cns_search_log')
            ->where('created_at', '>=', $del_duration)
            ->delete();

        // dd($res);

        return back()->with('success', 'Clear Successfull . ( ' . $res . ' Logs  affected)  ');
    }

    public function ban_keywords_index(Request $request)
    {
        $site_id = $request->site_id;
        $url = "/cliqnshop/keyword/ban";

        if ($site_id != null) {
            $url = "/cliqnshop/keyword/ban/".$site_id;

            $data = DB::connection('cliqnshop')->table('cns_ban_keywords')->where('site_id', $site_id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {

            $data = DB::connection('cliqnshop')->table('cns_ban_keywords')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        if ($request->ajax()) {         
            return Datatables::of($data)
            ->addColumn('action', function ($row) {
                return "<div class='d-flex'><a href='javascript:void(0)' name='id' id='edit' value ='$row->id' data-siteid='$row->site_id' data-keyword='$row->keyword' class='edit btn btn-success btn-sm'><i class='fas fa-edit'></i>Edit</a>"
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

        return view('Cliqnshop.keywordsearch.ban_keywords_index', compact('sites','url'));
    
    }

    public function store_ban_keywords(Request $request)
    {
        $keyword = $request->validate([
            'site' => 'required',
            'keyword' => 'required',
        ]);
        DB::connection('cliqnshop')->table('cns_ban_keywords')->insert([
            'site_id' => $keyword['site'],
            'keyword' => $keyword['keyword'],
            'created_at' => now(),
            'updated_at' => now(),

        ]);
        return response()->json(['Successfully']);
    }

    public function update_keyword(Request $request)
    {
        $keyword = $request->validate([
            'id' => 'required',
            'site' => 'required',
            'keyword' => 'required',
        ]);
        DB::connection('cliqnshop')->table('cns_ban_keywords')
        ->where('id',$keyword['id'],)
        ->update([
            'site_id' => $keyword['site'],
            'keyword' => $keyword['keyword'],
            'updated_at' => now(),
        ]);
        return response()->json(['Successfully']);
    }

    public function delete_keyword(Request $request)
    {
        $keyword = $request->validate([
            'id' => 'required',
        ]);
        DB::connection('cliqnshop')->table('cns_ban_keywords')
        ->where('id',$keyword['id'],)
        ->delete();
        return response()->json(['Successfully']);
    }    
}
