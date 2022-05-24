<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CountryStateCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries_states = json_decode(Storage::get('country.json'), true);
        $countries_lists = [];

        foreach($countries_states as $country)  {

            $countries_lists[] = [
                "name" => $country['name'], 
                "created_at" => now(),
                "updated_at" => now()    
            ];

        } // end of loop for countries

        $countries_count = DB::connection('inventory')->table('countries')->count();
        
        if($countries_count <= 0 ) {
            DB::connection('inventory')->table('countries')->insert($countries_lists);  
        }

        $countries = DB::connection('inventory')->table('countries')->get();
        $state_lists = [];

        foreach($countries as $country)
        {
           
            $key = array_search($country->name, array_column($countries_states, 'name'));

            $states = isset($countries_states[$key]['states' ]) ? $countries_states[$key]['states' ] : [];

            foreach($states as $state) {

                $state_lists[] = [
                    "country_id" => $country->id,
                    "name" => $state['name'],
                    "created_at" => now(),
                    "updated_at" => now()
                ];
            }

        }

        $states_count = DB::connection('inventory')->table('states')->count();

        if($states_count <= 0 ) {
            DB::connection('inventory')->table('states')->insert($state_lists);  
        }

        $states_get = DB::connection('inventory')->table('states')->get();
        $city_lists = [];
         
        foreach($states_get as $state)
        {

            $countries_get = DB::connection('inventory')->table('countries')->where('id', $state->country_id)->first();

            $country_key = array_search($countries_get->name, array_column($countries_states, 'name'));
            $states = isset($countries_states[$country_key]['states' ]) ? $countries_states[$country_key]['states' ] : [];

            $state_key = array_search($state->name, array_column($states, 'name'));
            $cities = isset($countries_states[$country_key]['states'][$state_key]['cities']) ? $countries_states[$country_key]['states'][$state_key]['cities'] : [];        
            
            $city_lists = [];
           
            foreach($cities as $city) {

                $city_lists[] = [
                    'state_id' => $state->id,
                    'name' => $city['name'],
                    'created_at' => now(),
                    'updated_at' => now()
                ];

            }

            DB::connection('inventory')->table('cities')->insert($city_lists);
         
        }
    }
    
}
