<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoDataFiller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pms:geo:mysql-seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function fillGeoCountriesTable()
    {
        try {
            if (DB::table('geo_countries')->exists()) {
                $this->warn('geo_countries table has data. Insertion skipped.');
            } else {
                $country_details_json_file_url = 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/countries.json';

                $get_country_details_json_file = Http::get($country_details_json_file_url);
                $get_country_details_json_file = json_decode($get_country_details_json_file);

                $countries = [];
                foreach ($get_country_details_json_file as $country) {
                    $temp = [
                        'id' => $country->id,
                        'code' => $country->iso2,
                        'iso3_code' => $country->iso3,
                        'name' => $country->name,
                        'currency_code' => $country->currency,
                        'currency_symbol' => $country->currency_symbol,
                        'currency_name' => $country->currency_name,
                        'mobile_prefix' => $country->phone_code,
                        'region' => $country->region,
                        'emoji_code' => $country->emojiU,
                        'latitude' => $country->latitude,
                        'longitude' => $country->longitude,
                    ];

                    array_push($countries, $temp);
                }

                DB::beginTransaction();
                DB::table('geo_countries')->insert($countries);
                DB::commit();
                $this->line('Filled geo_countries table.');
            }

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error occured while filling geo_countries table.', [$e->getMessage()]);
            $this->warn('Error occured while filling geo_countries table. Check your log.');

            return 0;
        }
    }

    public function fillGeoStatesTable()
    {
        try {
            if (DB::table('geo_states')->exists()) {
                $this->warn('geo_states table has data. Insertion skipped.');
            } else {
                $state_details_json_file_url = 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/states.json';

                $get_state_details_json_file = Http::get($state_details_json_file_url);
                $get_state_details_json_file = json_decode($get_state_details_json_file);

                $states = [];
                foreach ($get_state_details_json_file as $state) {
                    $temp = [
                        'id' => $state->id,
                        'geo_country_id' => $state->country_id,
                        'code' => $state->state_code,
                        'name' => $state->name,
                        'latitude' => $state->latitude,
                        'longitude' => $state->longitude,
                    ];

                    array_push($states, $temp);
                }

                DB::beginTransaction();

                foreach (array_chunk($states, 200) as $states_chunked_by_200) {
                    DB::table('geo_states')->insert($states_chunked_by_200);
                }
                DB::commit();
                $this->line('Filled geo_states table.');
            }

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error occured while filling geo_states table.', [$e->getMessage()]);
            $this->warn('Error occured while filling geo_states table. Check your log.');

            return 0;
        }
    }

    public function fillGeoCitiesTable()
    {
        try {
            if (DB::table('geo_cities')->exists()) {
                $this->warn('geo_cities table has data. Insertion skipped.');
            } else {
                $city_details_json_file_url = 'https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/cities.json';

                $get_city_details_json_file = Http::get($city_details_json_file_url);
                $get_city_details_json_file = json_decode($get_city_details_json_file);

                $cities = [];
                foreach ($get_city_details_json_file as $city) {
                    $temp = [
                        'id' => $city->id,
                        'geo_country_id' => $city->country_id,
                        'geo_state_id' => $city->state_id,
                        'name' => $city->name,
                        'latitude' => $city->latitude,
                        'longitude' => $city->longitude,
                    ];

                    array_push($cities, $temp);
                }

                DB::beginTransaction();

                foreach (array_chunk($cities, 200) as $cities_chunked_by_200) {
                    DB::table('geo_cities')->insert($cities_chunked_by_200);
                }
                DB::commit();
                $this->line('Filling geo_cities table. It may take 30s approximately.');
                $this->line('Filled geo_cities table.');
            }

            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error occured while filling geo_cities table.', [$e->getMessage()]);
            $this->warn('Error occured while filling geo_cities table. Check your log.');

            return 0;
        }
    }

    public function handle()
    {
        $this->fillGeoCountriesTable();
        $this->fillGeoStatesTable();
        $this->fillGeoCitiesTable();
        $this->info('Done');

        return 0;
    }
}
