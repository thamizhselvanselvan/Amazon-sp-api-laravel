<?php

namespace App\Http\Controllers\Cliqnshop;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FooterController extends Controller
{
    public function index()
    {
        return view('Cliqnshop.footer.footer');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'site' => 'required',
            'section' => 'required',
            'content' => 'required',
        ]);

        $now = carbon::now();
        $db = DB::connection('cliqnshop')->table('cns_footer_contents')
            ->updateOrInsert(
                ['site_name' => $data['site'], 'content_name' => $data['section']],
                ['content' => $data['content'], 'created_at' => $now, 'updated_at' => $now]
            );
        return back()->with('success', $data['section'] . ' has been Changed for ' . $data['site'] . ' Site');
    }

    public function staticpagecontent()
    {
        return view('Cliqnshop.footer.staticpagecontent');
    }

    

    public function getStaticPageContent(Request $request)
    {      

        $inputs= [
            'site' => 'required',
            'section' => 'required',
        ];        

        if($request->validate( $inputs))
        {
            $content_data = DB::connection('cliqnshop')->table('cns_footer_contents')->select('content')
            ->where("content_name",  $request->section)
            ->where("site_name", $request->site)
            ->first();   
            
            
            if ($content_data)
            {
                 return response()->json(array('content'=> $content_data->content), 200);
               
            }
            else
            {
                // \Illuminate\Support\Facades\Log::alert(' empty');
                return response()->json(array('content'=>'','error'=> 'no data found'), 200);
            }
        }
        else
        {
            \Illuminate\Support\Facades\Log::alert('validation failed');

            return response()->json(array('error'=> 'validation error'), 404);
        }
        
    }

}
