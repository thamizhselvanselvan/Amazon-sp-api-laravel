<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory\City;
use App\Models\Inventory\State;
use App\Models\Inventory\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Countrystatecity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:country-state-city';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get country, state and city data from json file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path =  public_path('country.json');
        $jsonfile = json_decode(file_get_contents($path), true);
        $countries_list = [];

        foreach ($jsonfile as $jsondata) {
            $countries_list[] = [

                "name" => $jsondata['name'],
                "country_code" => $jsondata['iso3'],
                "code" => $jsondata['iso2'],
                "numeric_code" => $jsondata['numeric_code'],
                "phone_code" => $jsondata['phone_code'],
                "capital" => $jsondata['capital'],
                "currency" => $jsondata['currency'],
                "currency_name" => $jsondata['currency_name'],
                "currency_symbol" => $jsondata['currency_symbol'],
                "created_at" => now(),
                "updated_at" => now(),
            ];
        }

        $country_count = Country::count();

        if ($country_count <= 0) {
            Country::insert($countries_list);
        }

        $countries = Country::get();
        $states_lists = [];

        foreach ($countries as $country) {
            $key = array_search($country->name, array_column($jsonfile, 'name'));
            $states = isset($jsonfile[$key]['states']) ? $jsonfile[$key]['states'] : [];

            foreach ($states as $state) {
                $states_lists[] = [

                    "country_id" => $country->id,
                    "name" => $state['name'],
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
            }
        }
        $states_count = State::count();
        if ($states_count <= 0) {
            State::insert($states_lists);
        }

        $states_name = State::get();


        foreach ($states_name as $state) {
            $countries_get = Country::where('id', $state->country_id)->first();

            $country_key = array_search($countries_get->name, array_column($jsonfile, 'name'));
            $total_states = isset($jsonfile[$country_key]['states']) ? $jsonfile[$country_key]['states'] : [];

            $state_key = array_search($state->name, array_column($total_states, 'name'));
            $cities = isset($jsonfile[$country_key]['states'][$state_key]['cities']) ? $jsonfile[$country_key]['states'][$state_key]['cities'] : [];

            $total_cities = [];
            foreach ($cities as $city) {
                $total_cities[] = [
                    "state_id" => $state->id,
                    "name" => $city['name'],
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
            }
            City::insert($total_cities);
        }
    }
}
