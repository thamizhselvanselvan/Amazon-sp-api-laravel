<?php

namespace App\Http\Controllers\Cliqnshop;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class CliqnshopVerificationController extends Controller
{
    public function verify_asin_index(Request $request)
    {
        if ($request->ajax()) {

            if ($request->has("site_id")) {
                //  \Illuminate\Support\Facades\Log::alert('validation failed');
               
                

                $data = DB::connection('cliqnshop')->table('mshop_product')      
                ->where('siteid', $request->site_id)            
                ->orderBy('mtime','desc');
            }
            else
            {
                $data = DB::connection('cliqnshop')->table('mshop_product')                  
                ->orderBy('mtime','desc');
            }

            

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('action', function ($data) {
                    $pid = $data->id;
                    $code = $data->code;
                    $site = $data->siteid;
                    $label = $data->label;
                    return  "<div class='d-flex'><a href=".route('cliqnshop.verify.asin.destroy', ['pid' => $pid,'code'=>$code,'site' => $site])." id='offers1' value ='$pid' data-label='$label' class='deleteAsin btn  bg-gradient-danger btn-xs'>Remove</a>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data['sites'] = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.verify.asin_verify',$data);

        
    }
    public function verify_asin_destroy(Request $request)
    {
        $this->validate($request,
                [
                    'pid' => ['required'], 
                    'code' => ['required'], 
                    'site' => ['required'],
                ]
            );
        
            
        $pid = $request->pid;
        $code = $request->code;
        $site = $request->site;

        commandExecFunc("mosh:remove_exported_asin ${site} ${pid}");
            
        return back()->with('warning', 'Product added to removable  list command !');
        
        

      
    }
    
    





}