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

            'country' => 'required|in:IN,UAE',
            'image' => 'required|in:Image-1,Image-2,Image-3',
            'url' => 'required',
            'img' => 'required|mimes:jpeg,png,jpg',

        ]);
        $now =  carbon::now();

        if (app()->environment() === 'local') {

            $file_path_img1 =  "local/3Banner_section/_image1.jpg";
            $file_path_img2 = "local/3Banner_section/_image2.jpg";
            $file_path_img3 = "local/3Banner_section/_image3.jpg";


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
                // Storage::put($file_path_img1, $img1);
                // $img1_url = 'Banner/_image1.jpg';
                Storage::disk('cliqnshop')->put($file_path_img1, $img1);
                $img1_url = Storage::disk('cliqnshop')->path('local/3Banner_section/_image1.jpg');
            } else if ($request->image == 'Image-2') {

                $img2 = file_get_contents($request->img);
                $urli2 = $request->url;
                // $img2_url = 'Banner/_image2.jpg';
                // Storage::put($file_path_img2, $img2);
                Storage::disk('cliqnshop')->put($file_path_img2, $img2);
                $img2_url = Storage::disk('cliqnshop')->path('local/3Banner_section/_image2.jpg');
            } else if ($request->image == 'Image-3') {

                $img3 = file_get_contents($request->img);
                $urli3 = $request->url;
                // Storage::put($file_path_img3, $img3);
                // $img3_url = 'Banner/_image3.jpg';
                Storage::disk('cliqnshop')->put($file_path_img3, $img3);
                $img3_url = Storage::disk('cliqnshop')->path('local/3Banner_section/_image3.jpg');
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
                    ->update(['content' => $data, 'country' => '1.', 'updated_at' => $now]);
            } else {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '3_banner_section_ae')
                    ->update(['content' => $data, 'country' => '2.', 'updated_at' => $now]);
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
                    ->update(['content' => $data, 'country' => '1.', 'updated_at' => $now]);
            } else {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '3_banner_section_ae')
                    ->update(['content' => $data, 'country' => '2.', 'updated_at' => $now]);
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
                    ->update(['content' => $data, 'country' => '1.', 'updated_at' => $now]);
            } else {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '3_banner_section_ae')
                    ->update(['content' => $data, 'country' => '2.', 'updated_at' => $now]);
            }
            return redirect()->route('cliqnshop.banner')->with('success', 'Image has Updated successfully');
        }
    }

    public function topselling(Request $request)
    {
        return view('Cliqnshop.imagebrand.asins');
    }

    public function storeasin(Request $request)
    {
        $request->validate([
            'country' => 'required|in:IN,UAE',
            'top_asin' => 'required',
        ]);

        $asins = preg_split('/[\r\n| |:|,]/', $request->top_asin, -1, PREG_SPLIT_NO_EMPTY);

        if (count($asins) > 20) {
            return redirect()->route('cliqnshop.brand')->with('error', 'Please Enter Less Than 20 ASIN');
        }

        $data = (json_encode($asins));
        $country = $request->country;
        $now =  carbon::now();
        if ($country == 'IN') {
            DB::connection('cliqnshop')->table('home_page_contents')
                ->where("section", '=',  'top_selling_products_section_in')
                ->update(['content' => $data, 'country' => '1.', 'updated_at' => $now]);
        } else {
            DB::connection('cliqnshop')->table('home_page_contents')
                ->where("section", '=',  'top_selling_products_section_ae')
                ->update(['content' => $data, 'country' => '2.', 'updated_at' => $now]);
        }

        return redirect()->route('cliqnshop.brand')->with('success', ' ASIN Inserted Successfuly');
    }


    public function twobannersection()
    {
        return view('Cliqnshop.imagebrand.twobanners');
    }

    public function two_bannerstore(Request $request)
    {
        $request->validate([

            'country' => 'required|in:IN,UAE',
            'image_number' => 'required|in:Image-1,Image-2',
            'url' => 'required',
            'selected_image' => 'required|mimes:jpeg,png,jpg',
        ]);
        $now = Carbon::now();
        if (app()->environment() === 'local') {
            $file_path_img1 =  "local/2Banner_section/_image1.jpg";
            $file_path_img2 =  "local/2Banner_section/_image2.jpg";



            $old_data = '';
            if ($request->country == 'IN') {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '2_banner_section_in')
                    ->get();
            } else {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '2_banner_section_ae')
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

            if ($request->image_number == 'Image-1') {
                $img1 = file_get_contents($request->selected_image);
                $urli1 = $request->url;
                Storage::put($file_path_img1, $img1);
                $img1_url = '2Banner_section/_image1.jpg';
                // Storage::disk('cliqnshop')->put($file_path_img1, $img1);
                // $img1_url = Storage::disk('cliqnshop')->path('local/3Banner_section/_image1.jpg');
            } else if ($request->image_number == 'Image-2') {

                $img2 = file_get_contents($request->selected_image);
                $urli2 = $request->url;
                $img2_url = '2Banner_section/_image2.jpg"';
                Storage::put($file_path_img2, $img2);
                // Storage::disk('cliqnshop')->put($file_path_img2, $img2);
                // $img2_url = Storage::disk('cliqnshop')->path('local/3Banner_section/_image2.jpg');
            }

            $two_banners = [
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
            ];
            $data =   ((json_encode($two_banners)));


            if ($request->country == 'IN') {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '2_banner_section_in')
                    ->update(['content' => $data, 'country' => '1.', 'updated_at' => $now]);
            } else {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '2_banner_section_ae')
                    ->update(['content' => $data, 'country' => '2.', 'updated_at' => $now]);
            }

            return redirect()->route('cliqnshop.twobanners')->with('success', 'Image has Updated successfully');
        }

        if (app()->environment() === 'staging') {

            $file_path_img1 =  "staging/Banner/2banner_image1.jpg";
            $file_path_img2 =  "staging/Banner/2banner/_image2.jpg";

            $old_data = '';
            if ($request->country == 'IN') {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '2_banner_section_in')
                    ->get();
            } else {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '2_banner_section_ae')
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


            if ($request->image_number == 'Image-1') {

                $img1 = file_get_contents($request->selected_image);
                $urli1 = $request->url;
                Storage::disk('cliqnshop')->put($file_path_img1, $img1);
                $img1_url = Storage::disk('cliqnshop')->path('staging/Banner/2banner_image1.jpg');
            } else if ($request->image_number == 'Image-2') {
                $img2 = file_get_contents($request->selected_image);
                $urli2 = $request->url;
                Storage::disk('cliqnshop')->put($file_path_img2, $img2);
                $img2_url = Storage::disk('cliqnshop')->path('staging/Banner/2banner_image2.jpg');
            }

            $two_banners = [
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
            ];
            $data =   ((json_encode($two_banners)));


            if ($request->country == 'IN') {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '2_banner_section_in')
                    ->update(['content' => $data, 'country' => '1.', 'updated_at' => $now]);
            } else {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '2_banner_section_ae')
                    ->update(['content' => $data, 'country' => '2.', 'updated_at' => $now]);
            }
            return redirect()->route('cliqnshop.banner')->with('success', 'Image has Updated successfully');
        }
        
        if (app()->environment() === 'production') {

            $file_path_img1 =  "production/Banner/2banner_image1.jpg";
            $file_path_img2 =  "production/Banner/2banner/_image2.jpg";

            $old_data = '';
            if ($request->country == 'IN') {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '2_banner_section_in')
                    ->get();
            } else {

                $old_data =   DB::connection('cliqnshop')->table('home_page_contents')
                    ->select('content')
                    ->where("section", '=',  '2_banner_section_ae')
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


            if ($request->image_number == 'Image-1') {

                $img1 = file_get_contents($request->selected_image);
                $urli1 = $request->url;
                Storage::disk('cliqnshop')->put($file_path_img1, $img1);
                $img1_url = Storage::disk('cliqnshop')->path('production/Banner/2banner_image1.jpg');
            } else if ($request->image_number == 'Image-2') {
                $img2 = file_get_contents($request->selected_image);
                $urli2 = $request->url;
                Storage::disk('cliqnshop')->put($file_path_img2, $img2);
                $img2_url = Storage::disk('cliqnshop')->path('production/Banner/2banner_image2.jpg');
            }

            $two_banners = [
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
            ];
            $data =   ((json_encode($two_banners)));


            if ($request->country == 'IN') {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '2_banner_section_in')
                    ->update(['content' => $data, 'country' => '1.', 'updated_at' => $now]);
            } else {
                DB::connection('cliqnshop')->table('home_page_contents')
                    ->where("section", '=',  '2_banner_section_ae')
                    ->update(['content' => $data, 'country' => '2.', 'updated_at' => $now]);
            }
            return redirect()->route('cliqnshop.banner')->with('success', 'Image has Updated successfully');
        }
    }
}
