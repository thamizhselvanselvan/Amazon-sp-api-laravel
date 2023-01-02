<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class cliqnshophomepageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $sections = [
            'top_selling_products_section',
            '1_banner_section',
            '2_banner_section',
            '3_banner_section',
            'trending_brands_section',
            'promo_banner_section'
        ];
        
        foreach ($sections as $section) {
            $datas = DB::connection('cliqnshop')->table('mshop_locale_site')->select('siteid')->get();
                foreach ($datas as $data) {
                $sector = DB::connection('cliqnshop')->table('home_page_contents')
                    ->where('section', $section)
                    ->where('country', $data->siteid)
                    ->get();

                if (count($sector)) {
                } else {
                    $datas = DB::connection('cliqnshop')->table('home_page_contents')
                        ->insert([
                            [
                                'section' => $section,
                                'content' => '',
                                'country' => $data->siteid,
                                "created_at" => now(),
                                "updated_at" => now()
                            ],
                        ]);
                }
            }
        }
    }
}
