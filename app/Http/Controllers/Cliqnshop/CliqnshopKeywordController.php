<?php

namespace App\Http\Controllers\Cliqnshop;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;


class CliqnshopKeywordController extends Controller
{
    public function keyword_search_log_index(Request $request)
    {

    
        $data = DB::connection('cliqnshop')->table('cns_search_log') 
        ->orderBy('created_at','desc')          
            ->get();

        // dd($data);
         
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()


                ->editColumn('created_at', function ($data) {
                   return $diw=  \Carbon\Carbon::parse($data->created_at)->diffForHumans();
                   
                })
                

                
                ->make(true);
        }

        return view('Cliqnshop.keywordsearch.keyword_search_index');
    }

    public function keyword_search_log_remove(Request $request)
    {
        


        switch ($request->select_timeline) {
            case ('l-1-h'):
                $del_duration  = Carbon::now()->subHours( 1 );
                break;

            case ('l-24-h'):
                $del_duration  = Carbon::now()->subHours( 24 );
                    break;
            
            case ('l-7-d'):
                $del_duration  = Carbon::now()->subDays( 7 );
                    break;   
                    
            case ('l-4-w'):
                $del_duration  = Carbon::now()->subWeeks( 4 );
                    break; 

            case ('all-time'):
                $del_duration  = '1-1-1';
                    break;
            default:
            return back()->with('error', 'Something went wrong!');
            
        }

         
         $res = DB::connection('cliqnshop')->table('cns_search_log')
                                    ->where('created_at', '>=', $del_duration)                                    
                                    ->delete();
        
        // dd($res);

        return back()->with('success', 'Clear Successfull . ( '.$res.' Logs  affected)  ');
    }
}
