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
        $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
            ->select('content')
            ->where("section", '=',  'banner')
            ->get();

        // $v = (json_decode($old_data));
        // $k = (json_decode($v['0']->content));
        // dd($k->banner2);
        // // dd($k->banner1->image);
        // // dd($k->banner1->country);


        return view('Cliqnshop.imagebrand.index');
    }
    public function storeimage(Request $request)
    {

        $year = Carbon::now()->isoformat('YYYY');


        if (app()->environment() === 'local') {
            $file_path_img1 = "Banner/_image1.jpg";
            $file_path_img2 = "Banner/_image2.jpg";
            $file_path_img3 = "Banner/_image3.jpg";


            if (($request->image == 'Select Image No.')) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please Choose Image Number');
            } else if (($request->country == 'Select Country')) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please Choose Country ');
            } else  if (($request->img == null)  && ($request->url == null)) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please select Image and Url');
            }

            $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                ->select('content')
                ->where("section", '=',  'banner')
                ->get();

            $val1 = (json_decode($old_data));
            $val2 = (json_decode($val1['0']->content));


            $country1 = '';
            $img1 = '';
            $urli1 = '';
            $img1_url = '';
            $country2 = '';
            $img2 = '';
            $urli2 = '';
            $img2_url = '';
            $country3 = '';
            $img3 = '';
            $urli3 = '';
            $img3_url = '';
            if (isset($val2->banner1)) {

                $country1 = $val2->banner1->country;
                $img1 = '';
                $urli1 = $val2->banner1->url;
                $img1_url = $val2->banner1->image;
            }

            if (isset($val2->banner2)) {
                $country2 = $val2->banner2->country;
                $img2 = '';
                $urli2 = $val2->banner2->url;
                $img2_url = $val2->banner2->image;
            }
            if (isset($val2->banner3)) {
                $country3 = $val2->banner3->country;
                $img3 = '';
                $urli3 = $val2->banner3->url;
                $img3_url = $val2->banner3->image;
            }




            if ($request->image == 'Image-1') {
                $img1 = file_get_contents($request->img);
                $urli1 = $request->url;
                Storage::put($file_path_img1, $img1);
                $img1_url = Storage::path('Banner/_image1.jpg');
                $country1 = $request->country;
            } else if ($request->image == 'Image-2') {

                $img2 = file_get_contents($request->img);
                $urli2 = $request->url;
                $img2_url = Storage::path('Banner/_image2.jpg');
                Storage::put($file_path_img2, $img2);
                $country2 = $request->country;
            } else if ($request->image == 'Image-3') {

                $img3 = file_get_contents($request->img);
                Storage::put($file_path_img3, $img3);
                $urli3 = $request->url;
                $img3_url = Storage::path('Banner/_image3.jpg');
                $country3 = $request->country;
            }


            $three_banners = [
                'banner1' =>
                [
                    'url' => $urli1,
                    'image' =>   $img1_url,
                    'country' => $country1
                ],
                'banner2' =>
                [
                    'url' =>  $urli2,
                    'image' =>   $img2_url,
                    'country' => $country2
                ],
                'banner3' =>
                [
                    'url' =>  $urli3,
                    'image' =>  $img3_url,
                    'country' =>  $country3
                ],
            ];
            $data =   ((json_encode($three_banners)));
            DB::connection('cliqnshop')->table('home_page_contents')
                ->where("section", '=',  'banner')
                ->update(['content' => $data]);

            return redirect()->route('cliqnshop.banner')->with('success', 'Image has Updated successfully');
        }


        if (app()->environment() === 'staging') {
            $file_path_img1 = "Banner/_image1.jpg";
            $file_path_img2 = "Banner/_image2.jpg";
            $file_path_img3 = "Banner/_image3.jpg";


            if (($request->image == 'Select Image No.')) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please Choose Image Number');
            } else if (($request->country == 'Select Country')) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please Choose Country ');
            } else  if (($request->img == null)  && ($request->url == null)) {
                return redirect()->route('cliqnshop.banner')->with('error', 'Please select Image and Url');
            }
            $country = $request->country;
            $img1 = '';
            $urli1 = '';
            $img2 = '';
            $urli2 = '';
            $img3 = '';
            $urli3 = '';

            if ($request->image == 'Image-1') {

                $img1 = file_get_contents($request->img);
                $urli1 = $request->url;
                Storage::disk('do')->put($file_path_img1, $img1);
            } else if ($request->image == 'Image-2') {
                $img2 = file_get_contents($request->img);
                $urli2 = $request->url;
                Storage::disk('do')->put($file_path_img2, $img2);
            } else if ($request->image == 'Image-3') {
                $img3 = file_get_contents($request->img);
                $urli3 = $request->url;
                Storage::disk('do')->put($file_path_img3, $img3);
            }

            $three_banners = [
                'banner1' =>
                [
                    'url' => $urli1,
                    'image' =>  '',
                    'country' => $country
                ],
                'banner2' =>
                [
                    'url' =>  $urli2,
                    'image' =>  '',
                    'country' => $country
                ],
                'banner3' =>
                [
                    'url' =>  $urli3,
                    'image' => '',
                    'country' => $country
                ],
            ];
            $data =   ((json_encode($three_banners)));

            DB::connection('cliqnshop')->table('home_page_contents')
                ->where("section", '=',  'banner')
                ->update(['content' => $data]);
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
