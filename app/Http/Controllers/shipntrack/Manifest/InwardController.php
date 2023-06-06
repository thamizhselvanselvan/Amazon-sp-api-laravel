<?php

namespace App\Http\Controllers\shipntrack\Manifest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ShipNtrack\Process\Process_Master;
use App\Models\ShipNTrack\Inventory\Manifest_Master;

class InwardController extends Controller
{
    public function index(Request $request)
    {
        return view('shipntrack.manifest.inward.index');
    }
    public function inw_view(Request $request)
    {
        $destinations  = Process_Master::query()->get();
        return view('shipntrack.manifest.inward.add', compact('destinations'));
    }
    public function inw_data_fech(Request $request)
    {
        $awb = $request->awb;
        $mode = $request->mode;

        $articles = DB::connection('shintracking')->table('articles')
        ->join('categories', 'articles.id', '=', 'categories.id')
        ->join('users', 'users.id', '=', 'articles.user_id')
        ->select('articles.id', 'articles.title', 'articles.body', 'users.username', 'category.name')
        ->get();

        // $response = $this->fetchdata($request);

        // if (count($response) > 0) {
        //     return response()->json(['success' => 'success', 'data' =>   $response]);
        // } else {
        //     return response()->json(["error" => 'No data Found..Please Check The Manifest ID']);
        // }
    }
}
