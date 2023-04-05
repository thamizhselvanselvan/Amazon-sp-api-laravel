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
                ->orderBy('mtime','desc')
                ->get();
            }
            else
            {
                $data = DB::connection('cliqnshop')->table('mshop_product')                  
                ->orderBy('mtime','desc')
                ->get();
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

        $domains = [
            'attribute' =>  [
                'table_name' => 'mshop_attribute',
                'direct_delete' => false,
                'isHandled' => false
            ],
            'media' =>  [
                'table_name' => 'mshop_media',
                'direct_delete' => false,
                'isHandled' => false
            ],
            'price' =>  [
                'table_name' => 'mshop_price',
                'direct_delete' => true,
                'isHandled' => false
            ],
            'text' =>  [
                'table_name' => 'mshop_text',
                'direct_delete' => true,
                'isHandled' => false
            ],
            'supplier' =>  [
                'table_name' => 'mshop_supplier',
                'direct_delete' => false,
                'isHandled' => false
            ],
            'keyword' =>  [
                'table_name' => 'mshop_keyword',
                'direct_delete' => false,
                'isHandled' => false
            ],
        ];


        foreach ($domains as $key => $value) 
        {
            $domain = $key ;
            $table_name = $value['table_name']; 
            $direct_delete = $value['direct_delete']; 

            // \Illuminate\Support\Facades\Log::alert('table_name:'. $table_name . ',domain:'.$domain. 'direct_delete:'.$direct_delete);

           
            $mshop_product_list = DB::connection('cliqnshop')->table('mshop_product_list')
            ->where(['parentid' => $pid, 'domain' => $domain,  'siteid' => $site])
            ->select('refid','domain')->get();

            

                if($this->byAsinDomainRemover($domain,$table_name,$direct_delete,$request->toArray(),$mshop_product_list))
                {                    
                    DB::connection('cliqnshop')->table('mshop_product_list')
                    ->where(['parentid' => $pid, 'domain' => $domain,  'siteid' => $site])
                    ->delete();              
                    
                }

                $value['isHandled'] = true;
                $domains[$domain] = $value;
                           
                      
        }

        // removing mshop_product[main table] if all the domains are handled --start
        
            $allHandled = true;
            foreach ($domains as $domain ) 
            {
                if(!$domain['isHandled'])
                {
                    $allHandled = false ;
                    break;
                }            
            }
            if($allHandled)
            {

                $qryMshopStockRemove = DB::connection('cliqnshop')->table('mshop_stock')
                ->where(['prodid' => $pid, 'siteid' => $site])
                ->delete();

                $qryMshopProductRemove = DB::connection('cliqnshop')->table('mshop_product')
                ->where(['id' => $pid, 'siteid' => $site])
                ->delete();
                return back()->with('success', 'Product removed !');
            }
        // removing mshop_product[main table] if all the domains are handled --end 
        
        

      
    }
    
    public function byAsinDomainRemover($domain,$table_name,$direct_delete,$request,$mshop_product_list) :bool 
    {
        
        if ($direct_delete) 
        {

            foreach ($mshop_product_list as $singleItem ) 
            {
                $refId = $singleItem->refid;
                $site = $request['site'];
                $domainListRemoverQry =  DB::connection('cliqnshop')->table($table_name)
                        ->where(['id' => $refId, 'siteid' => $site])
                        ->delete();
                                
            }
            return true;
           
        }
        else
        {
            foreach ($mshop_product_list as $singleItem ) 
            {
                $refId = $singleItem->refid;
                $site = $request['site'];
                $productId = $request['pid'];

                $domainListRemoverQry = DB::connection('cliqnshop')->table('mshop_product_list')
                ->where([ 'domain' => $domain,  'siteid' => $site , 'refid' => $refId])
                ->whereNotIn('parentid' , [$productId])
                ->select('refid','domain','parentid')->get();

                if(count($domainListRemoverQry)==0)
                {
                    DB::connection('cliqnshop')->table($table_name)
                        ->where(['id' => $refId, 'siteid' => $site])
                        ->delete();
                }
            }

            return true;

        }

        return false;
        
    }





}