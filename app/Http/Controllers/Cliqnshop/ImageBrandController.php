<?php

namespace App\Http\Controllers\Cliqnshop;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Aws_credential;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use SellingPartnerApi\Endpoint;
use Illuminate\Support\Facades\Log;
use SellingPartnerApi\Configuration;

use SellingPartnerApi\Api\ReportsV20210630Api as ReportsApi;

class ImageBrandController extends Controller
{
    public function threebanner(Request $request)
    {
        $countrys = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.imagebrand.threebanner', compact('countrys'));
    }

    public function storeimage(Request $request)
    {
        $request->validate([

            'country' => 'required',
            'image' => 'required|in:Image-1,Image-2,Image-3',
            'url' => 'required',
            'img' => 'required|mimes:jpeg,png,jpg',

        ]);
        $now =  carbon::now();
        if (app()->environment() === 'local') {
            $old_data = '';
            $old_data = DB::connection('cliqnshop')->table('cns_home_page_contents')->select('content')
                ->where("section", '3_banner_section')
                ->where("country", $request->country)
                ->get();
            $val1 = (json_decode($old_data));
            $img1 = '';
            $urli1 = '';
            $img1_url = '';

            $img2 = '';
            $urli2 = '';
            $img2_url = '';

            $img3 = '';
            $urli3 = '';
            $img3_url = '';
            if (isset($val1['0']->content)) {
                $val2 = (json_decode($val1['0']->content));
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
            }
            if ($request->image == 'Image-1') {
                $img1 = file_get_contents($request->img);
                $urli1 = $request->url;
                // Storage::put('local/banner/3banner/' . substr($request->country, 0, -1) . '/image1.jpg', $img1);
                Storage::disk('cliqnshop')->put('local/banner/3banner/' . substr($request->country, 0, -1) . '/image1.jpg', $img1);
                $img1_url = Storage::disk('cliqnshop')->path('local/banner/3banner/' . substr($request->country, 0, -1) . '/image1.jpg');
            } else if ($request->image == 'Image-2') {

                $img2 = file_get_contents($request->img);
                $urli2 = $request->url;
                Storage::disk('cliqnshop')->put('local/banner/3banner/' . substr($request->country, 0, -1) . '/image2.jpg', $img2);
                $img2_url = Storage::disk('cliqnshop')->path('local/banner/3banner/' . substr($request->country, 0, -1) . '/image2.jpg');
            } else if ($request->image == 'Image-3') {

                $img3 = file_get_contents($request->img);
                $urli3 = $request->url;
                Storage::disk('cliqnshop')->put('local/banner/3banner/' . substr($request->country, 0, -1) . '/image3.jpg', $img3);
                $img3_url = Storage::disk('cliqnshop')->path('local/banner/3banner/' . substr($request->country, 0, -1) . '/image3.jpg');
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
            $condition = strval($request->country);
            DB::connection('cliqnshop')->table('cns_home_page_contents')
                ->where('section', '3_banner_section')
                ->where('country', $condition)
                ->update(['content' => $data,  'updated_at' => $now]);
            return redirect()->route('cliqnshop.banner')->with('success', 'Image has Updated successfully');
        }

        if (app()->environment() === 'staging') {
            $old_data = '';
            $old_data = DB::connection('cliqnshop')->table('cns_home_page_contents')->select('content')
                ->where("section", '3_banner_section')
                ->where("country", $request->country)
                ->get();
            $val1 = (json_decode($old_data));
            $img1 = '';
            $urli1 = '';
            $img1_url = '';

            $img2 = '';
            $urli2 = '';
            $img2_url = '';

            $img3 = '';
            $urli3 = '';
            $img3_url = '';
            if (isset($val1['0']->content)) {
                $val2 = (json_decode($val1['0']->content));
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
            }
            if ($request->image == 'Image-1') {
                $img1 = file_get_contents($request->img);
                $urli1 = $request->url;
                // Storage::put('staging/banner/3banner/' . substr($request->country, 0, -1) . '/image1.jpg', $img1);
                Storage::disk('cliqnshop')->put('staging/banner/3banner/' . substr($request->country, 0, -1) . '/image1.jpg', $img1);
                $img1_url = Storage::disk('cliqnshop')->path('staging/banner/3banner/' . substr($request->country, 0, -1) . '/image1.jpg');
            } else if ($request->image == 'Image-2') {

                $img2 = file_get_contents($request->img);
                $urli2 = $request->url;
                Storage::disk('cliqnshop')->put('staging/banner/3banner/' . substr($request->country, 0, -1) . '/image2.jpg', $img2);
                $img2_url = Storage::disk('cliqnshop')->path('staging/banner/3banner/' . substr($request->country, 0, -1) . '/image2.jpg');
            } else if ($request->image == 'Image-3') {

                $img3 = file_get_contents($request->img);
                $urli3 = $request->url;
                Storage::disk('cliqnshop')->put('staging/banner/3banner/' . substr($request->country, 0, -1) . '/image3.jpg', $img3);
                $img3_url = Storage::disk('cliqnshop')->path('staging/banner/3banner/' . substr($request->country, 0, -1) . '/image3.jpg');
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
            $condition = strval($request->country);
            DB::connection('cliqnshop')->table('cns_home_page_contents')
                ->where('section', '3_banner_section')
                ->where('country', $condition)
                ->update(['content' => $data,  'updated_at' => $now]);
            return redirect()->route('cliqnshop.banner')->with('success', 'Image has Updated successfully');
        }

        if (app()->environment() === 'production') {
            $old_data = '';
            $old_data = DB::connection('cliqnshop')->table('cns_home_page_contents')->select('content')
                ->where("section", '3_banner_section')
                ->where("country", $request->country)
                ->get();
            $val1 = (json_decode($old_data));
            $img1 = '';
            $urli1 = '';
            $img1_url = '';

            $img2 = '';
            $urli2 = '';
            $img2_url = '';

            $img3 = '';
            $urli3 = '';
            $img3_url = '';
            if (isset($val1['0']->content)) {
                $val2 = (json_decode($val1['0']->content));
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
            }
            if ($request->image == 'Image-1') {
                $img1 = file_get_contents($request->img);
                $urli1 = $request->url;
                // Storage::put('production/banner/3banner/' . substr($request->country, 0, -1) . '/image1.jpg', $img1);
                Storage::disk('cliqnshop')->put('production/banner/3banner/' . substr($request->country, 0, -1) . '/image1.jpg', $img1);
                $img1_url = Storage::disk('cliqnshop')->path('production/banner/3banner/' . substr($request->country, 0, -1) . '/image1.jpg');
            } else if ($request->image == 'Image-2') {

                $img2 = file_get_contents($request->img);
                $urli2 = $request->url;
                Storage::disk('cliqnshop')->put('production/banner/3banner/' . substr($request->country, 0, -1) . '/image2.jpg', $img2);
                $img2_url = Storage::disk('cliqnshop')->path('production/banner/3banner/' . substr($request->country, 0, -1) . '/image2.jpg');
            } else if ($request->image == 'Image-3') {

                $img3 = file_get_contents($request->img);
                $urli3 = $request->url;
                Storage::disk('cliqnshop')->put('production/banner/3banner/' . substr($request->country, 0, -1) . '/image3.jpg', $img3);
                $img3_url = Storage::disk('cliqnshop')->path('production/banner/3banner/' . substr($request->country, 0, -1) . '/image3.jpg');
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
            $condition = strval($request->country);
            DB::connection('cliqnshop')->table('cns_home_page_contents')
                ->where('section', '3_banner_section')
                ->where('country', $condition)
                ->update(['content' => $data,  'updated_at' => $now]);
            return redirect()->route('cliqnshop.banner')->with('success', 'Image has Updated successfully');
        }
    }

    public function topselling(Request $request)
    {
        $country = $request->country;
        if ($request->ajax()) {
            $data = DB::connection('cliqnshop')->table('cns_home_page_contents')
                ->where('section', 'top_selling_products_section')
                ->where('country', $country)
                ->select('content')
                ->get()->pluck('content');

            if ($data['0'] != '') {
                $data = implode(",", json_decode($data[0], true));
            } else {
                $data = "";
            }
            return response()->json(['success' => 'Data  successfully Fetched', 'data' => $data]);
        }
        $countrys = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.imagebrand.asins', compact('countrys'));
    }

    public function storeasin(Request $request)
    {
        $request->validate([
            'country' => 'required',
            'top_asin' => 'required',
        ]);

        $asins = preg_split('/[\r\n| |:|,]/', $request->top_asin, -1, PREG_SPLIT_NO_EMPTY);

        if (count($asins) > 100) {
            return redirect()->route('cliqnshop.brand')->with('error', 'Please Enter Less Than 100 ASIN');
        }

        $data = (json_encode($asins));
        $country = $request->country;
        $now =  carbon::now();
        $condition = strval($request->country);
        DB::connection('cliqnshop')->table('cns_home_page_contents')
            ->where('section', 'top_selling_products_section')
            ->where('country', $condition)
            ->update(['content' => $data,  'updated_at' => $now]);

        return redirect()->route('cliqnshop.brand')->with('success', ' ASIN Inserted Successfuly');
    }

    public function twobannersection()
    {
        $countrys = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.imagebrand.twobanners', compact('countrys'));
    }

    public function two_bannerstore(Request $request)
    {
        $request->validate([
            'country' => 'required',
            'image_number' => 'required|in:Image-1,Image-2',
            'url' => 'required',
            'selected_image' => 'required|mimes:jpeg,png,jpg',
        ]);

        $country = $request->country;
        $env = '';
        $Imgno = $request->image_number;
        $imgselected = $request->selected_image;
        $image_url = $request->url;

        if (app()->environment() === 'staging') {
            $env = 'staging';
        } elseif (app()->environment() === 'production') {
            $env = 'production';
        } else {
            $env = 'local';
        }

        $this->Two_banner($country, $Imgno, $imgselected, $image_url, $env);
        return redirect()->route('cliqnshop.twobanners')->with('success', 'Image has Updated successfully');
    }

    public function Two_banner($country, $Imgno, $imgselected, $image_url, $env)
    {
        $string  = substr($country, 0, -1);

        $path1 = "$env/banner/2banner/$string/image1.jpg";
        $path2 = "$env/banner/2banner/$string/image2.jpg";

        $now = Carbon::now();
        $old_data = '';
        $old_data = DB::connection('cliqnshop')->table('cns_home_page_contents')->select('content')
            ->where("section", '2_banner_section')
            ->where("country", $country)
            ->get();

        $val1 = (json_decode($old_data));

        $img1 = '';
        $urli1 = '';
        $img1_url = '';

        $img2 = '';
        $urli2 = '';
        $img2_url = '';

        if (isset($val1['0']->content)) {
            $val2 = (json_decode($val1['0']->content));
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
        }

        if ($Imgno == 'Image-1') {
            $img1 = file_get_contents($imgselected);
            $urli1 =  $image_url;
            // Storage::put($path1, $img1);
            // $img1_url = $path1;
            Storage::disk('cliqnshop')->put($path1, $img1);
            $img1_url = $path1;
        } else if ($Imgno == 'Image-2') {
            $img2 = file_get_contents($imgselected);
            $urli2 =  $image_url;
            Storage::put($path2, $img2);
            $img2_url = $path2;
            Storage::disk('cliqnshop')->put($path2, $img2);
            // $img2_url = Storage::disk('cliqnshop')->path($path2);
            $img2_url = $path2;
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
        $data =   json_encode($two_banners);
        $condition = strval($country);

        DB::connection('cliqnshop')->table('cns_home_page_contents')
            ->where('section', '2_banner_section')
            ->where('country', $condition)
            ->update(['content' => $data,  'updated_at' => $now]);
    }

    public function onebanner(Request $request)
    {

        if ($request->ajax()) {
            $data = '';
           
            if (app()->environment() === 'staging') {
                $data =  Storage::disk('cliqnshop')->temporaryUrl('staging/banner/1banner/' . substr($request->country, 0, -1) . '/image1.jpg', '+2 minutes');
            } else if (app()->environment() === 'production') {
               $data =   Storage::disk('cliqnshop')->temporaryUrl('production/banner/1banner/' . substr($request->country, 0, -1) . '/image1.jpg', '+2 minutes');
            } else {
                $data =  Storage::disk('cliqnshop')->temporaryUrl('local/banner/1banner/' . substr($request->country, 0, -1) . '/image1.jpg', '+2 minutes');
            }
            return ['success' => 'Data  successfully Fetched', 'data' => $data];
        }
        $countrys = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.imagebrand.onebanner', compact('countrys'));
    }

    public function one_bannerstore(Request $request)
    {
        $now = Carbon::now();
        $request->validate([

            'country' => 'required',
            'primary_text' => 'required',
            'secondary_text' => 'required',
            'url' => 'required',
            'selected_image' => 'required|mimes:jpeg,png,jpg',

        ]);
        if (app()->environment() === 'local') {
            $old_data = '';
            $old_data = DB::connection('cliqnshop')->table('cns_home_page_contents')->select('content')
                ->where("section", '1_banner_section')
                ->where("country", $request->country)
                ->get();

            $val1 = (json_decode($old_data));
            $imageurl = '';
            $image = '';
            $primary_text = '';
            $secondary_text = '';
            if (isset($val1['0']->content)) {
                $val2 = (json_decode($val1['0']->content));

                if (isset($val2->banner1)) {
                    $imageurl = $val2->banner1->url;
                    $image = $val2->banner1->image;
                    $primary_text = $val2->banner1->primary_text;
                    $secondary_text = $val2->banner1->secondary_text;
                }
            }

            if ($request->selected_image) {

                $image = file_get_contents($request->selected_image);
                $imageurl = $request->url;
                $primary_text = $request->primary_text;
                $secondary_text = $request->secondary_text;
                // Storage::put('local/banner/1banner/' . substr($request->country, 0, -1) . '/_image1.jpg', $image);
                // $url_do = 'local/banner/1banner/' . substr($request->country, 0, -1) . '/_image1.jpg';
                Storage::disk('cliqnshop')->put('local/banner/1banner/' . substr($request->country, 0, -1) . '/image1.jpg', $image);
                $url_do = Storage::disk('cliqnshop')->path('local/banner/1banner/' . substr($request->country, 0, -1) . '/image1.jpg');
            }

            $one_banners = [
                'banner1' =>
                [
                    'url' => $imageurl,
                    'image' =>   $url_do,
                    'primary_text' => $primary_text,
                    'secondary_text' => $secondary_text,
                ],
            ];
            $data =   ((json_encode($one_banners)));

            $condition = strval($request->country);

            DB::connection('cliqnshop')->table('cns_home_page_contents')
                ->where('section', '1_banner_section')
                ->where('country', $condition)
                ->update(['content' => $data,  'updated_at' => $now]);
            return redirect()->route('cliqnshop.onebanner')->with('success', 'Image has Updated successfully');
        }
        if (app()->environment() === 'staging') {
            $old_data = '';
            $old_data = DB::connection('cliqnshop')->table('cns_home_page_contents')->select('content')
                ->where("section", '1_banner_section')
                ->where("country", $request->country)
                ->get();

            $val1 = (json_decode($old_data));
            $imageurl = '';
            $image = '';
            $primary_text = '';
            $secondary_text = '';
            if (isset($val1['0']->content)) {
                $val2 = (json_decode($val1['0']->content));

                if (isset($val2->banner1)) {
                    $imageurl = $val2->banner1->url;
                    $image = $val2->banner1->image;
                    $primary_text = $val2->banner1->primary_text;
                    $secondary_text = $val2->banner1->secondary_text;
                }
            }

            if ($request->selected_image) {
                $image = file_get_contents($request->selected_image);
                $imageurl = $request->url;
                $primary_text = $request->primary_text;
                $secondary_text = $request->secondary_text;
                Storage::disk('cliqnshop')->put('staging/banner/1banner/' . substr($request->country, 0, -1) . '/image1.jpg', $image);
                $url_do = Storage::disk('cliqnshop')->path('staging/banner/1banner/' . substr($request->country, 0, -1) . '/image1.jpg');
            }

            $one_banners = [
                'banner1' =>
                [
                    'url' => $imageurl,
                    'image' =>   $url_do,
                    'primary_text' => $primary_text,
                    'secondary_text' => $secondary_text,
                ],
            ];
            $data =   ((json_encode($one_banners)));

            $condition = strval($request->country);

            DB::connection('cliqnshop')->table('cns_home_page_contents')
                ->where('section', '1_banner_section')
                ->where('country', $condition)
                ->update(['content' => $data,  'updated_at' => $now]);
            return redirect()->route('cliqnshop.onebanner')->with('success', 'Image has Updated successfully');
        }
        if (app()->environment() === 'production') {
            $old_data = '';
            $old_data = DB::connection('cliqnshop')->table('cns_home_page_contents')->select('content')
                ->where("section", '1_banner_section')
                ->where("country", $request->country)
                ->get();

            $val1 = (json_decode($old_data));
            $imageurl = '';
            $image = '';
            $primary_text = '';
            $secondary_text = '';
            if (isset($val1['0']->content)) {
                $val2 = (json_decode($val1['0']->content));

                if (isset($val2->banner1)) {
                    $imageurl = $val2->banner1->url;
                    $image = $val2->banner1->image;
                    $primary_text = $val2->banner1->primary_text;
                    $secondary_text = $val2->banner1->secondary_text;
                }
            }

            if ($request->selected_image) {
                $image = file_get_contents($request->selected_image);
                $imageurl = $request->url;
                $primary_text = $request->primary_text;
                $secondary_text = $request->secondary_text;
                Storage::disk('cliqnshop')->put('production/banner/1banner/' . substr($request->country, 0, -1) . '/image1.jpg', $image);
                $url_do = Storage::disk('cliqnshop')->path('production/banner/1banner/' . substr($request->country, 0, -1) . '/image1.jpg');
            }

            $one_banners = [
                'banner1' =>
                [
                    'url' => $imageurl,
                    'image' =>   $url_do,
                    'primary_text' => $primary_text,
                    'secondary_text' => $secondary_text,
                ],
            ];
            $data =   ((json_encode($one_banners)));

            $condition = strval($request->country);

            DB::connection('cliqnshop')->table('cns_home_page_contents')
                ->where('section', '1_banner_section')
                ->where('country', $condition)
                ->update(['content' => $data,  'updated_at' => $now]);
            return redirect()->route('cliqnshop.onebanner')->with('success', 'Image has Updated successfully');
        }
    }

    public function trendingbrandssection()
    {
        $countrys = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.imagebrand.trending', compact('countrys'));
    }

    public function trendingbrands(Request $request)
    {
        $now = Carbon::now();
        $request->validate([
            'country' => 'required',
            'url' => 'required',
            'image_number' => 'required|in:Image-1,Image-2,Image-3,Image-4,Image-5,Image-6',
            'selected_image' => 'required|mimes:jpeg,png,jpg',
        ]);

        if (app()->environment() === 'local') {
            $old_data = DB::connection('cliqnshop')->table('cns_home_page_contents')->select('content')
                ->where("section", 'trending_brands_section')
                ->where("country", $request->country)
                ->get();
            $img1 = '';
            $urli1 = '';
            $img1_url = '';

            $img2 = '';
            $urli2 = '';
            $img2_url = '';

            $img3 = '';
            $urli3 = '';
            $img3_url = '';

            $img4 = '';
            $urli4 = '';
            $img4_url = '';

            $img5 = '';
            $urli5 = '';
            $img5_url = '';

            $img6 = '';
            $urli6 = '';
            $img6_url = '';
            $val1 = (json_decode($old_data));
            if (isset($val1['0']->content)) {

                $val2 = (json_decode($val1['0']->content));


                if (isset($val2->banner1)) {
                    $urli1 = $val2->banner1->url;
                    $img1_url = $val2->banner1->image;
                }
                if (isset($val2->banner2)) {
                    $urli2 = $val2->banner2->url;
                    $img2_url = $val2->banner2->image;
                }

                if (isset($val2->banner3)) {
                    $urli3 = $val2->banner3->url;
                    $img3_url = $val2->banner3->image;
                }

                if (isset($val2->banner4)) {
                    $urli4 = $val2->banner4->url;
                    $img4_url = $val2->banner4->image;
                }

                if (isset($val2->banner5)) {
                    $urli5 = $val2->banner5->url;
                    $img5_url = $val2->banner5->image;
                }

                if (isset($val2->banner6)) {
                    $urli6 = $val2->banner6->url;
                    $img6_url = $val2->banner6->image;
                }
            }


            if ($request->image_number == 'Image-1') {
                $img1 = file_get_contents($request->selected_image);
                $urli1 = $request->url;
                // Storage::put('local/banner/trending/'.substr($request->country, 0, -1).'/_image1.jpg', $img1);
                // $img1_url = 'local/banner/trending/'.substr($request->country, 0, -1).'/_image1.jpg';
                Storage::disk('cliqnshop')->put('local/banner/trending/' . substr($request->country, 0, -1) . '/image1.jpg', $img1);
                $img1_url = Storage::disk('cliqnshop')->path('local/banner/trending/' . substr($request->country, 0, -1) . '/image1.jpg');
            } else 
            if ($request->image_number == 'Image-2') {
                $img2 = file_get_contents($request->selected_image);
                $urli2 = $request->url;
                Storage::disk('cliqnshop')->put('local/banner/trending/' . substr($request->country, 0, -1) . '/image2.jpg', $img2);
                $img2_url = Storage::disk('cliqnshop')->path('local/banner/trending/' . substr($request->country, 0, -1) . '/image2.jpg');
            }
            if ($request->image_number == 'Image-3') {
                $img3 = file_get_contents($request->selected_image);
                $urli3 = $request->url;
                Storage::disk('cliqnshop')->put('local/banner/trending/' . substr($request->country, 0, -1) . '/image3.jpg', $img3);
                $img3_url = Storage::disk('cliqnshop')->path('local/banner/trending/' . substr($request->country, 0, -1) . '/image3.jpg');
            }
            if ($request->image_number == 'Image-4') {
                $img4 = file_get_contents($request->selected_image);
                $urli4 = $request->url;
                Storage::disk('cliqnshop')->put('local/banner/trending/' . substr($request->country, 0, -1) . '/image4.jpg', $img4);
                $img4_url = Storage::disk('cliqnshop')->path('local/banner/trending/' . substr($request->country, 0, -1) . '/image4.jpg');
            }
            if ($request->image_number == 'Image-5') {
                $img5 = file_get_contents($request->selected_image);
                $urli5 = $request->url;
                Storage::disk('cliqnshop')->put('local/banner/trending/' . substr($request->country, 0, -1) . '/image5.jpg', $img5);
                $img5_url = Storage::disk('cliqnshop')->path('local/banner/trending/' . substr($request->country, 0, -1) . '/image5.jpg');
            }
            if ($request->image_number == 'Image-6') {
                $img6 = file_get_contents($request->selected_image);
                $urli6 = $request->url;
                Storage::disk('cliqnshop')->put('local/banner/trending/' . substr($request->country, 0, -1) . '/image6.jpg', $img6);
                $img6_url = Storage::disk('cliqnshop')->path('local/banner/trending/' . substr($request->country, 0, -1) . '/image6.jpg');
            }

            $trending_brands = [
                'banner1' =>
                [
                    'url' => $urli1,
                    'image' =>   $img1_url,
                ],
                'banner2' =>
                [
                    'url' =>  $urli2,
                    'image' =>  $img2_url,
                ],
                'banner3' =>
                [
                    'url' =>  $urli3,
                    'image' =>  $img3_url,
                ],
                'banner4' =>
                [
                    'url' =>  $urli4,
                    'image' =>  $img4_url,
                ],
                'banner5' =>
                [
                    'url' =>  $urli5,
                    'image' =>  $img5_url,
                ],
                'banner6' =>
                [
                    'url' =>  $urli6,
                    'image' =>  $img6_url,
                ],
            ];
            $data =  (json_encode($trending_brands));
            $condition = strval($request->country);

            DB::connection('cliqnshop')->table('cns_home_page_contents')
                ->where('section', 'trending_brands_section')
                ->where('country', $condition)
                ->update(['content' => $data,  'updated_at' => $now]);

            return redirect()->route('cliqnshop.trending')->with('success', 'Image has Updated successfully');
        }

        if (app()->environment() === 'staging') {
            $old_data = DB::connection('cliqnshop')->table('cns_home_page_contents')->select('content')
                ->where("section", 'trending_brands_section')
                ->where("country", $request->country)
                ->get();
            $img1 = '';
            $urli1 = '';
            $img1_url = '';

            $img2 = '';
            $urli2 = '';
            $img2_url = '';

            $img3 = '';
            $urli3 = '';
            $img3_url = '';

            $img4 = '';
            $urli4 = '';
            $img4_url = '';

            $img5 = '';
            $urli5 = '';
            $img5_url = '';

            $img6 = '';
            $urli6 = '';
            $img6_url = '';
            $val1 = (json_decode($old_data));
            if (isset($val1['0']->content)) {

                $val2 = (json_decode($val1['0']->content));


                if (isset($val2->banner1)) {
                    $urli1 = $val2->banner1->url;
                    $img1_url = $val2->banner1->image;
                }
                if (isset($val2->banner2)) {
                    $urli2 = $val2->banner2->url;
                    $img2_url = $val2->banner2->image;
                }

                if (isset($val2->banner3)) {
                    $urli3 = $val2->banner3->url;
                    $img3_url = $val2->banner3->image;
                }

                if (isset($val2->banner4)) {
                    $urli4 = $val2->banner4->url;
                    $img4_url = $val2->banner4->image;
                }

                if (isset($val2->banner5)) {
                    $urli5 = $val2->banner5->url;
                    $img5_url = $val2->banner5->image;
                }

                if (isset($val2->banner6)) {
                    $urli6 = $val2->banner6->url;
                    $img6_url = $val2->banner6->image;
                }
            }


            if ($request->image_number == 'Image-1') {
                $img1 = file_get_contents($request->selected_image);
                $urli1 = $request->url;
                Storage::disk('cliqnshop')->put('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image1.jpg', $img1);
                $img1_url = Storage::disk('cliqnshop')->path('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image1.jpg');
            } else 
            if ($request->image_number == 'Image-2') {
                $img2 = file_get_contents($request->selected_image);
                $urli2 = $request->url;
                Storage::disk('cliqnshop')->put('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image2.jpg', $img2);
                $img2_url = Storage::disk('cliqnshop')->path('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image2.jpg');
            }
            if ($request->image_number == 'Image-3') {
                $img3 = file_get_contents($request->selected_image);
                $urli3 = $request->url;
                Storage::disk('cliqnshop')->put('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image3.jpg', $img3);
                $img3_url = Storage::disk('cliqnshop')->path('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image3.jpg');
            }
            if ($request->image_number == 'Image-4') {
                $img4 = file_get_contents($request->selected_image);
                $urli4 = $request->url;
                Storage::disk('cliqnshop')->put('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image4.jpg', $img4);
                $img4_url = Storage::disk('cliqnshop')->path('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image4.jpg');
            }
            if ($request->image_number == 'Image-5') {
                $img5 = file_get_contents($request->selected_image);
                $urli5 = $request->url;
                Storage::disk('cliqnshop')->put('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image5.jpg', $img5);
                $img5_url = Storage::disk('cliqnshop')->path('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image5.jpg');
            }
            if ($request->image_number == 'Image-6') {
                $img6 = file_get_contents($request->selected_image);
                $urli6 = $request->url;
                Storage::disk('cliqnshop')->put('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image6.jpg', $img6);
                $img6_url = Storage::disk('cliqnshop')->path('staging/banner/trending/' . substr($request->country, 0, -1) . '/_image6.jpg');
            }

            $trending_brands = [
                'banner1' =>
                [
                    'url' => $urli1,
                    'image' =>   $img1_url,
                ],
                'banner2' =>
                [
                    'url' =>  $urli2,
                    'image' =>  $img2_url,
                ],
                'banner3' =>
                [
                    'url' =>  $urli3,
                    'image' =>  $img3_url,
                ],
                'banner4' =>
                [
                    'url' =>  $urli4,
                    'image' =>  $img4_url,
                ],
                'banner5' =>
                [
                    'url' =>  $urli5,
                    'image' =>  $img5_url,
                ],
                'banner6' =>
                [
                    'url' =>  $urli6,
                    'image' =>  $img6_url,
                ],
            ];
            $data =  (json_encode($trending_brands));
            $condition = strval($request->country);

            DB::connection('cliqnshop')->table('cns_home_page_contents')
                ->where('section', 'trending_brands_section')
                ->where('country', $condition)
                ->update(['content' => $data,  'updated_at' => $now]);

            return redirect()->route('cliqnshop.trending')->with('success', 'Image has Updated successfully');
        }

        if (app()->environment() === 'production') {
            $old_data = DB::connection('cliqnshop')->table('cns_home_page_contents')->select('content')
                ->where("section", 'trending_brands_section')
                ->where("country", $request->country)
                ->get();
            $img1 = '';
            $urli1 = '';
            $img1_url = '';

            $img2 = '';
            $urli2 = '';
            $img2_url = '';

            $img3 = '';
            $urli3 = '';
            $img3_url = '';

            $img4 = '';
            $urli4 = '';
            $img4_url = '';

            $img5 = '';
            $urli5 = '';
            $img5_url = '';

            $img6 = '';
            $urli6 = '';
            $img6_url = '';
            $val1 = (json_decode($old_data));
            if (isset($val1['0']->content)) {

                $val2 = (json_decode($val1['0']->content));


                if (isset($val2->banner1)) {
                    $urli1 = $val2->banner1->url;
                    $img1_url = $val2->banner1->image;
                }
                if (isset($val2->banner2)) {
                    $urli2 = $val2->banner2->url;
                    $img2_url = $val2->banner2->image;
                }

                if (isset($val2->banner3)) {
                    $urli3 = $val2->banner3->url;
                    $img3_url = $val2->banner3->image;
                }

                if (isset($val2->banner4)) {
                    $urli4 = $val2->banner4->url;
                    $img4_url = $val2->banner4->image;
                }

                if (isset($val2->banner5)) {
                    $urli5 = $val2->banner5->url;
                    $img5_url = $val2->banner5->image;
                }

                if (isset($val2->banner6)) {
                    $urli6 = $val2->banner6->url;
                    $img6_url = $val2->banner6->image;
                }
            }

            if ($request->image_number == 'Image-1') {
                $img1 = file_get_contents($request->selected_image);
                $urli1 = $request->url;
                Storage::disk('cliqnshop')->put('production/banner/trending/' . substr($request->country, 0, -1) . '/_image1.jpg', $img1);
                $img1_url = Storage::disk('cliqnshop')->path('production/banner/trending/' . substr($request->country, 0, -1) . '/_image1.jpg');
            } else 
            if ($request->image_number == 'Image-2') {
                $img2 = file_get_contents($request->selected_image);
                $urli2 = $request->url;
                Storage::disk('cliqnshop')->put('production/banner/trending/' . substr($request->country, 0, -1) . '/_image2.jpg', $img2);
                $img2_url = Storage::disk('cliqnshop')->path('production/banner/trending/' . substr($request->country, 0, -1) . '/_image2.jpg');
            }
            if ($request->image_number == 'Image-3') {
                $img3 = file_get_contents($request->selected_image);
                $urli3 = $request->url;
                Storage::disk('cliqnshop')->put('production/banner/trending/' . substr($request->country, 0, -1) . '/_image3.jpg', $img3);
                $img3_url = Storage::disk('cliqnshop')->path('production/banner/trending/' . substr($request->country, 0, -1) . '/_image3.jpg');
            }
            if ($request->image_number == 'Image-4') {
                $img4 = file_get_contents($request->selected_image);
                $urli4 = $request->url;
                Storage::disk('cliqnshop')->put('production/banner/trending/' . substr($request->country, 0, -1) . '/_image4.jpg', $img4);
                $img4_url = Storage::disk('cliqnshop')->path('production/banner/trending/' . substr($request->country, 0, -1) . '/_image4.jpg');
            }
            if ($request->image_number == 'Image-5') {
                $img5 = file_get_contents($request->selected_image);
                $urli5 = $request->url;
                Storage::disk('cliqnshop')->put('production/banner/trending/' . substr($request->country, 0, -1) . '/_image5.jpg', $img5);
                $img5_url = Storage::disk('cliqnshop')->path('production/banner/trending/' . substr($request->country, 0, -1) . '/_image5.jpg');
            }
            if ($request->image_number == 'Image-6') {
                $img6 = file_get_contents($request->selected_image);
                $urli6 = $request->url;
                Storage::disk('cliqnshop')->put('production/banner/trending/' . substr($request->country, 0, -1) . '/_image6.jpg', $img6);
                $img6_url = Storage::disk('cliqnshop')->path('production/banner/trending/' . substr($request->country, 0, -1) . '/_image6.jpg');
            }

            $trending_brands = [
                'banner1' =>
                [
                    'url' => $urli1,
                    'image' =>   $img1_url,
                ],
                'banner2' =>
                [
                    'url' =>  $urli2,
                    'image' =>  $img2_url,
                ],
                'banner3' =>
                [
                    'url' =>  $urli3,
                    'image' =>  $img3_url,
                ],
                'banner4' =>
                [
                    'url' =>  $urli4,
                    'image' =>  $img4_url,
                ],
                'banner5' =>
                [
                    'url' =>  $urli5,
                    'image' =>  $img5_url,
                ],
                'banner6' =>
                [
                    'url' =>  $urli6,
                    'image' =>  $img6_url,
                ],
            ];
            $data =  (json_encode($trending_brands));
            $condition = strval($request->country);

            DB::connection('cliqnshop')->table('cns_home_page_contents')
                ->where('section', 'trending_brands_section')
                ->where('country', $condition)
                ->update(['content' => $data,  'updated_at' => $now]);

            return redirect()->route('cliqnshop.trending')->with('success', 'Image has Updated successfully');
        }
    }

    public function promobanner()
    {
        $countrys = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid', 'code')->get();
        return view('Cliqnshop.imagebrand.promobanner', compact('countrys'));
    }
    public function promostore(Request $request)
    {

        $request->validate([

            'country' => 'required',
            'offer_text' => 'required',
            'primary_text' => 'required',
            'secondary_text' => 'required',
            'url' => 'required',

        ]);

        $country = $request->country;
        $offer_text = $request->offer_text;
        $primary_text = $request->primary_text;
        $secondary_text = $request->secondary_text;
        $url = $request->url;
        $now =  carbon::now();

        $data = [
            'primary_text'  => $primary_text,
            'secondary_text'  => $secondary_text,
            'offer_text'  => $offer_text,
            'url'  => $url,
        ];
        $condition = strval($request->country);
        DB::connection('cliqnshop')->table('cns_home_page_contents')
            ->where('section', 'promo_banner_section')
            ->where('country', $condition)
            ->update(['content' => json_encode($data),  'updated_at' => $now]);
        return redirect()->route('cliqnshop.promo.banner')->with('success', ' Data Inserted Successfuly...!');
    }


}
