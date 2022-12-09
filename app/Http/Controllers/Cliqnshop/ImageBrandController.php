<?php

namespace App\Http\Controllers\Cliqnshop;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ImageBrandController extends Controller
{
    public function index(Request $request)
    {

        return view('Cliqnshop.imagebrand.index');
    }
    public function storeimage(Request $request)
    {

        $request->validate([

            'country' => 'required',
            'image' => 'required',
            'url' => 'required',
            'img' => 'required|mimes:jpeg,png,jpg',

        ]);

        if (app()->environment() === 'local') {
            $file_path_img1 = "Banner/_image1.jpg";
            $file_path_img2 = "Banner/_image2.jpg";
            $file_path_img3 = "staging/Banner/_image3.jpg";


            if (($request->image == 'Select Image No.')) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please Choose Image Number');
            } else if (($request->country == 'Select Country')) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please Choose Country ');
            } else  if (($request->img == null)  && ($request->url == null)) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please select Image and Url');
            }


            $old_data = '';
            if ($request->country == 'IN') {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '3_banner_section_in')
                    ->get();
            } else {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '3_banner_section_ae')
                    ->get();
            }



            $val1 = (json_decode($old_data));
            $val2 = (json_decode($val1['0']->content));

            $img1 = '';
            $urli1 = '';
            $img1_url = '';

            $img2 = '';
            $urli2 = '';
            $img2_url = '';

            $img3 = '';
            $urli3 = '';
            $img3_url = '';
            if (isset($val2->banner1)) {


                $img1 = '';
                $urli1 = $val2->banner1->url;
                $img1_url = $val2->banner1->image;
            }
            if (isset($val2->banner2)) {

                $img2 = '';
                $urli2 = $val2->banner2->url;
                $img2_url = $val2->banner2->image;
            }
            if (isset($val2->banner3)) {

                $img3 = '';
                $urli3 = $val2->banner3->url;
                $img3_url = $val2->banner3->image;
            }

            if ($request->image == 'Image-1') {
                $img1 = file_get_contents($request->img);
                $urli1 = $request->url;
                Storage::put($file_path_img1, $img1);
                $img1_url = Storage::path('Banner/_image1.jpg');
            } else if ($request->image == 'Image-2') {

                $img2 = file_get_contents($request->img);
                $urli2 = $request->url;
                $img2_url = Storage::path('Banner/_image2.jpg');
                Storage::put($file_path_img2, $img2);
            } else if ($request->image == 'Image-3') {

                $img3 = file_get_contents($request->img);
                Log::alert($img3);
                Log::alert($file_path_img3);
                $urli3 = $request->url;
                Storage::put($file_path_img3, $img3);
                $img3_url = Storage::path('Banner/_image3.jpg');
            }

            $three_banners = [
                'banner1' =>
                [
                    'url' => $urli1,
                    'image' =>   $img1_url,
                ],
                'banner2' =>
                [
                    'url' =>  $urli2,
                    'image' =>   $img2_url,
                ],
                'banner3' =>
                [
                    'url' =>  $urli3,
                    'image' =>  $img3_url,
                ],
            ];
            $data =   ((json_encode($three_banners)));





            if ($request->country == 'IN') {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '3_banner_section_in')
                    ->where("country", '=', '1.')
                    ->update(['content' => $data]);
            } else {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '3_banner_section_ae')
                    ->where("country", '=',  '2.')
                    ->update(['content' => $data]);
            }

            return redirect()->route('cliqnshop.banner')->with('success', 'Image has Updated successfully');
        }


        if (app()->environment() === 'staging') {
          
            $file_path_img1 =  "staging/Banner/_image1.jpg";
            $file_path_img2 =  "staging/Banner/_image2.jpg";
            $file_path_img3 =  "staging/Banner/_image3.jpg";


            if (($request->image == 'Select Image No.')) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please Choose Image Number');
            } else if (($request->country == 'Select Country')) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please Choose Country ');
            } else  if (($request->img == null)  && ($request->url == null)) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please select Image and Url');
            }


            $old_data = '';
            if ($request->country == 'IN') {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '3_banner_section_in')
                    ->get();
            } else {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '3_banner_section_ae')
                    ->get();
            }



            $val1 = (json_decode($old_data));
            $val2 = (json_decode($val1['0']->content));

            $img1 = '';
            $urli1 = '';
            $img1_url = '';

            $img2 = '';
            $urli2 = '';
            $img2_url = '';

            $img3 = '';
            $urli3 = '';
            $img3_url = '';
            if (isset($val2->banner1)) {


                $img1 = '';
                $urli1 = $val2->banner1->url;
                $img1_url = $val2->banner1->image;
            }
            if (isset($val2->banner2)) {

                $img2 = '';
                $urli2 = $val2->banner2->url;
                $img2_url = $val2->banner2->image;
            }
            if (isset($val2->banner3)) {

                $img3 = '';
                $urli3 = $val2->banner3->url;
                $img3_url = $val2->banner3->image;
            }




            if ($request->image == 'Image-1') {

                $img1 = file_get_contents($request->img);
                $urli1 = $request->url;
                Storage::disk('cliqnshop')->put($file_path_img1, $img1);
                $img1_url = Storage::disk('cliqnshop')->path('staging/Banner/_image1.jpg');
            } else if ($request->image == 'Image-2') {
                $img2 = file_get_contents($request->img);
                $urli2 = $request->url;
                Storage::disk('cliqnshop')->put($file_path_img2, $img2);
                $img2_url = Storage::disk('cliqnshop')->path('staging/Banner/_image2.jpg');
            } else if ($request->image == 'Image-3') {
                $img3 = file_get_contents($request->img);
                $urli3 = $request->url;
                Storage::disk('cliqnshop')->put($file_path_img3, $img3);
                $img3_url = Storage::disk('cliqnshop')->path('staging/Banner/_image3.jpg');
            }

            $three_banners = [
                'banner1' =>
                [
                    'url' => $urli1,
                    'image' =>   $img1_url,
                ],
                'banner2' =>
                [
                    'url' =>  $urli2,
                    'image' =>   $img2_url,
                ],
                'banner3' =>
                [
                    'url' =>  $urli3,
                    'image' =>  $img3_url,
                ],
            ];
            $data =   ((json_encode($three_banners)));


            if ($request->country == 'IN') {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '3_banner_section_in')
                    ->where("country", '=', '1.')
                    ->update(['content' => $data]);
            } else {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '3_banner_section_ae')
                    ->where("country", '=',  '2.')
                    ->update(['content' => $data]);
            }
            return redirect()->route('cliqnshop.banner')->with('success', 'Image has Updated successfully');
        }


        if (app()->environment() === 'production') {

            $file_path_img1 =  "production/Banner/_image1.jpg";
            $file_path_img2 =  "production/Banner/_image2.jpg";
            $file_path_img3 =  "production/Banner/_image3.jpg";

            if (($request->image == 'Select Image No.')) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please Choose Image Number');
            } else if (($request->country == 'Select Country')) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please Choose Country ');
            } else  if (($request->img == null)  && ($request->url == null)) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please select Image and Url');
            }
            $old_data = '';
            if ($request->country == 'IN') {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '3_banner_section_in')
                    ->get();
            } else {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '3_banner_section_ae')
                    ->get();
            }

            $val1 = (json_decode($old_data));
            $val2 = (json_decode($val1['0']->content));

            $img1 = '';
            $urli1 = '';
            $img1_url = '';

            $img2 = '';
            $urli2 = '';
            $img2_url = '';

            $img3 = '';
            $urli3 = '';
            $img3_url = '';
            if (isset($val2->banner1)) {
                $img1 = '';
                $urli1 = $val2->banner1->url;
                $img1_url = $val2->banner1->image;
            }
            if (isset($val2->banner2)) {

                $img2 = '';
                $urli2 = $val2->banner2->url;
                $img2_url = $val2->banner2->image;
            }
            if (isset($val2->banner3)) {

                $img3 = '';
                $urli3 = $val2->banner3->url;
                $img3_url = $val2->banner3->image;
            }

            if ($request->image == 'Image-1') {

                $img1 = file_get_contents($request->img);
                $urli1 = $request->url;
                Storage::disk('cliqnshop')->put($file_path_img1, $img1);
                $img1_url = Storage::disk('cliqnshop')->path('production/Banner/_image1.jpg');
            } else if ($request->image == 'Image-2') {
                $img2 = file_get_contents($request->img);
                $urli2 = $request->url;
                Storage::disk('cliqnshop')->put($file_path_img2, $img2);
                $img2_url = Storage::disk('cliqnshop')->path('production/Banner/_image2.jpg');
            } else if ($request->image == 'Image-3') {
                $img3 = file_get_contents($request->img);
                $urli3 = $request->url;
                Storage::disk('cliqnshop')->put($file_path_img3, $img3);
                $img3_url = Storage::disk('cliqnshop')->path('production/Banner/_image3.jpg');
            }

            $three_banners = [
                'banner1' =>
                [
                    'url' => $urli1,
                    'image' =>   $img1_url,
                ],
                'banner2' =>
                [
                    'url' =>  $urli2,
                    'image' =>   $img2_url,
                ],
                'banner3' =>
                [
                    'url' =>  $urli3,
                    'image' =>  $img3_url,
                ],
            ];
            $data =   ((json_encode($three_banners)));


            if ($request->country == 'IN') {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '3_banner_section_in')
                    ->where("country", '=', '1.')
                    ->update(['content' => $data]);
            } else {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '3_banner_section_ae')
                    ->where("country", '=',  '2.')
                    ->update(['content' => $data]);
            }
            return redirect()->route('cliqnshop.banner')->with('success', 'Image has Updated successfully');
        }
    }



    public function storeasin(Request $request)
    {
        if ($request->ajax()) {

            $asins = preg_split('/[\r\n| |:|,]/', $request->asin, -1, PREG_SPLIT_NO_EMPTY);
        }

        foreach ($asins as $key => $asin) {

            $list[] = [
                'section' =>  'top_selling_products_section',
                'content' =>  $asin,
                'created_at' => now(),
                'updated_at' => now()
            ];
            DB::connection('cliqnshop')->table('home_page_contents')->insert($list);
        }

        return redirect()->route('cliqnshop.banner')->with('success', 'AINS  has Updated successfully');
    }
}
