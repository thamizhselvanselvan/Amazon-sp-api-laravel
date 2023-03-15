<?php

namespace App\Http\Controllers\Cliqnshop;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;


class CliqnshopCategoryController extends Controller
{
    public function mshop_category_lister(Request $request)
    {

    
        $data = DB::connection('cliqnshop')->table('mshop_catalog') 
        ->select('mshop_catalog.*', 'cns_ban_category.category_code','cns_ban_category.created_at')
        ->leftjoin('cns_ban_category','cns_ban_category.category_code','=','mshop_catalog.code')
        ->orderBy('ctime','desc')          
        ->get();

        // dd($data);
         
        if ($request->ajax()) {
            return Datatables::of($data)
                ->addIndexColumn()


                ->editColumn('ctime', function ($data) {
                   return $diw=  \Carbon\Carbon::parse($data->ctime)->diffForHumans();
                   
                })

               
                ->editColumn('created_at', function ($data) {
                    if(!is_null($data->created_at))
                        return $diw=  \Carbon\Carbon::parse($data->created_at)->diffForHumans();
                    else
                        return '-';                    
                 })

                ->addColumn('action', function ($data) {
                    $id = $data->id;
            

                    $isChecked = !is_null($data->category_code) ?'checked':'';
                    return  "<div class='form-group'>
                                        <div class='custom-control custom-switch custom-switch-off-success custom-switch-on-danger'>
                                        <input type='checkbox' data-siteid='".$data->siteid."' data-catlabel='".$data->label."' value=".$data->code." 
                                        ".$isChecked."
                                        class='custom-control-input actionSwitch' id='actionSwitch".$id."'>
                                        <label class='custom-control-label' for='actionSwitch".$id."'></label>
                                        </div>
                                        </div>";

                    // return  "<input  data-status=0 class='actionSwitch' value=".$data->code." data-siteid=".$data->siteid."  name='actionSwitch' id='actionSwitch' type= 'checkbox'>";

                }) 
                

                
                ->make(true);
        }

        return view('Cliqnshop.category.mshop_category_lister');
    }

    public function storebancategory(Request $request)
    {
        if ($request->ajax()) 
        {

            $inputs= [
                'siteid' => 'required',
                'catCode' => 'required',
                'operation' => 'required',
            ];    

            if($request->validate( $inputs))
            {   
                if($request->operation == "add" )
                {
                    $operration = DB::connection('cliqnshop')
                                ->table('cns_ban_category')
                                ->insert(['site_id' => $request->siteid,
                                             'category_code' => $request->catCode,
                                             'created_at' => \Carbon\Carbon::now(),
                                             'updated_at' => \Carbon\Carbon::now()
                                            ]);   
                    
                    return response()->json(array('message'=> 'adding sucess'), 200);
                }
                else if ($request->operation == "remove")
                {
                    $operration =   DB::connection('cliqnshop')
                                    ->table('cns_ban_category')
                                    ->where('category_code', $request->catCode)
                                    ->delete();

                    return response()->json(array('message'=> 'remove sucess'), 200);
                }
                else
                {
                    return response()->json(array('error'=> 'Sorry ! Something went Wrong'), 404);
                }
            }
            else
            {
                // \Illuminate\Support\Facades\Log::alert('validation failed');
    
                return response()->json(array('error'=> 'validation error'), 404);
            }

        }
        else
        {
            return 'Not Allowed!!!!!';
        }

        
    }

   
}
