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
                ->where(['siteid' => $request->site_id])
                ->whereIn('editor',['cns_search_from_in','cns_search_from_uae'])       
                ->orderBy('mtime','desc');
            }
            else
            {
                $data = DB::connection('cliqnshop')->table('mshop_product')
                ->whereIn('editor',['cns_search_from_in','cns_search_from_uae'])          
                ->orderBy('mtime','desc');
            }

            

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('action',function ($data) {
                    $pid = $data->id;
                    $asin = $data->asin;
                    $code = $data->code;
                    $site = $data->siteid;
                    $label = $data->label;
                    $status = $data->status;
                    $actionbtn = '';
                    if ($status == 0) {
                        $actionbtn = "<div class='d-flex'><a href=".route('cliqnshop.verify.asin.approve', ['pid' => $pid, 'asin' => $asin, 'site' => $site])." id='offers2' value ='$pid' data-label='$label' class='approveAsin btn  bg-gradient-success btn-xs mr-2'>Approve</a>";
                        }
                        $actionbtn .= "<div class='d-flex'><a href=".route('cliqnshop.verify.asin.destroy', ['pid' => $pid,'code'=>$code, 'asin' => $asin, 'site' => $site])." id='offers1' value ='$pid' data-label='$label' class='deleteAsin btn  bg-gradient-danger btn-xs'>Remove</a>";
                        return $actionbtn;
                })
                ->addColumn('site', function ($data) {
                    $s_code = DB::connection('cliqnshop')->table('mshop_locale_site')->where('siteid',$data->siteid)->pluck('code')->ToArray();
                    if ($s_code[0] == 'uae')
                    {
                        return '<center><p class = "text-danger">UAE</p></center>';
                    }
                    if ($s_code[0] == 'in')
                    {
                        return '<center><p class = "text-success">India</p></center>';
                    }
                })
                ->addColumn('status',function ($data) {
                    $status = $data->status;
                    if ($status == 0)
                    {
                        return '<center><p class = "text-danger">Disable</p></center>';
                    }
                    else
                    {
                        return '<center><p class = "text-success">Enable</p></center>';
                    }
                })
                ->rawColumns(['action','site','status'])
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
                    'asin' => ['required']
                ]
            );
        
            
        $pid = $request->pid;
        $code = $request->code;
        $site = $request->site;
        $asin = $request->asin;

        DB::connection('cliqnshop')->table('cns_ban_asin')->insert(
            [
                'site_id' => $site,
                'asin'    => $asin,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        commandExecFunc("mosh:remove_exported_asin ${site} ${pid}");
            
        return back()->with('warning', $asin .' '.'added to removable  list command ! and added to permanent ban');
        
        

      
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

           $check_price = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site])->where('domain','price')->get()->ToArray();

           $check_image = DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site])->where('domain','media')->get()->ToArray();

           if ($check_price == []) {
            return back()->with('error', "Price Not Found! You can't approve" . ' '. $asin);
           }

           if ($check_image == []) {
            return back()->with('error', "Image Not Found! You can't approve" . ' '. $asin);
           }

           if($check_price !== [] && $check_image !== [])
           {
            DB::connection('cliqnshop')->table('mshop_product')->where(['id' => $pid, 'siteid' => $site])->update(['status' => 1, 'ctime' => now(), 'mtime' => now()]);
            DB::connection('cliqnshop')->table('mshop_product_list')->where(['parentid' => $pid, 'siteid' => $site])->update(['status' => 1, 'mtime' => now()]);

            return back()->with('success', 'Approved' .' '. $asin);
           }
    }





}