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
}
