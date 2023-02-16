<?php

namespace App\Console\Commands\V2;

use Illuminate\Console\Command;
use App\Models\V2\Masters\Currency as MastersCurrency;
use App\Models\V2\Masters\Region;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CurrencyRegion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'v2:currency-region-insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'insert currency regions';

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
        $currencies = array(
            array('id' => '1','name' => 'US Dollor','code' => 'USD','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '2','name' => 'British Pound','code' => 'GBP','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '3','name' => 'Euro','code' => 'EUR','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '4','name' => 'Japanese yen','code' => 'JPY','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '5','name' => 'Canadian dollar','code' => 'CAD','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '6','name' => 'Chinese yuan','code' => 'CNY','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '7','name' => 'Indian Rupee','code' => 'INR','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '8','name' => 'Australian dollar','code' => 'AUD','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '9','name' => 'Brazilian real','code' => 'BRL','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '10','name' => 'Mexican peso','code' => 'MXN','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '11','name' => 'Turkish lira','code' => 'TRY','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '12','name' => 'United Arab Emirates dirham','code' => 'AED','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '13','name' => 'Saudi riyal','code' => 'SAR','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '14','name' => 'Singapore dollar','code' => 'SGD','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '15','name' => 'Swedish krona','code' => 'SEK','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '16','name' => 'Polish złoty','code' => 'PLN','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '17','name' => '	Egyptian pound','code' => 'EGP','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL)
          );
          MastersCurrency::insert($currencies);

          $regions = array(
            array('id' => '1','currency_id' => '9','region' => 'Brazil','region_code' => 'BR','url' => 'https://mws.amazonservices.com/','site_url' => 'https://www.amazon.com.br/','marketplace_id' => 'A2Q3Y263D00KWC','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '2','currency_id' => '5','region' => 'Canada','region_code' => 'CA','url' => 'https://mws.amazonservices.ca/','site_url' => 'https://www.amazon.ca/','marketplace_id' => 'A2EUQ1WTGCTBG2','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '3','currency_id' => '10','region' => 'Mexico','region_code' => 'MX','url' => 'https://mws.amazonservices.com.mx','site_url' => 'https://www.amazon.com.mx/','marketplace_id' => 'A1AM78C64UM0Y8','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '4','currency_id' => '1','region' => 'US','region_code' => 'US','url' => 'https://mws.amazonservices.com/','site_url' => 'https://www.amazon.com/','marketplace_id' => 'ATVPDKIKX0DER','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '5','currency_id' => '12','region' => 'United Arab Emirates (U.A.E.)','region_code' => 'AE','url' => 'https://mws.amazonservices.ae','site_url' => 'https://www.amazon.ae/','marketplace_id' => 'A2VIGQ35RCS4UG','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '6','currency_id' => '3','region' => 'Germany','region_code' => 'DE','url' => 'https://mws-eu.amazonservices.com','site_url' => 'https://www.amazon.de/','marketplace_id' => 'A1PA6795UKMFR9','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '7','currency_id' => '17','region' => 'Egypt','region_code' => 'EG','url' => '  https://mws-eu.amazonservices.com/','site_url' => 'https://www.amazon.eg/','marketplace_id' => 'ARBP9OOSHTCHU','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '8','currency_id' => '3','region' => 'Spain','region_code' => 'ES','url' => 'https://mws-eu.amazonservices.com/','site_url' => 'https://www.amazon.es/','marketplace_id' => 'A1RKKUPIHCS9HS','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '9','currency_id' => '3','region' => 'France','region_code' => 'FR','url' => 'https://mws-eu.amazonservices.com/','site_url' => 'https://www.amazon.fr/','marketplace_id' => 'A13V1IB3VIYZZH','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '10','currency_id' => '2','region' => 'UK','region_code' => 'UK','url' => 'https://mws-eu.amazonservices.com/','site_url' => 'https://www.amazon.co.uk/','marketplace_id' => 'A1F83G8C2ARO7P','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '11','currency_id' => '7','region' => 'India','region_code' => 'IN','url' => 'https://mws.amazonservices.in/','site_url' => 'https://amazon.in/','marketplace_id' => 'A21TJRUUN4KGV','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '12','currency_id' => '3','region' => 'Italy','region_code' => 'IT','url' => '  https://mws-eu.amazonservices.com/','site_url' => 'https://www.amazon.it/','marketplace_id' => 'APJ6JRA9NG5V4','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '13','currency_id' => '3','region' => 'Netherlands','region_code' => 'NL','url' => 'https://mws-eu.amazonservices.com/','site_url' => 'https://www.amazon.nl/','marketplace_id' => 'A1805IZSGTT6HS','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '14','currency_id' => '16','region' => 'Poland','region_code' => 'PL','url' => 'https://mws-eu.amazonservices.com/','site_url' => 'https://www.amazon.pl/','marketplace_id' => 'A1C3SOZRARQ6R3','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '15','currency_id' => '13','region' => 'Saudi Arabia','region_code' => 'SA','url' => 'https://mws-eu.amazonservices.com/','site_url' => 'https://www.amazon.sa/','marketplace_id' => 'A17E79C6D8DWNP','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '16','currency_id' => '15','region' => 'Sweden','region_code' => 'SE','url' => 'https://mws.amazonservices.in/','site_url' => 'https://www.amazon.se/','marketplace_id' => 'A2NODRKZP88ZB9','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '17','currency_id' => '11','region' => 'Turkey','region_code' => 'TR','url' => 'https://mws.amazonservices.in/','site_url' => 'https://www.amazon.com.tr/','marketplace_id' => 'A33AVAJ2PDY3EV','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '18','currency_id' => '14','region' => 'Singapore','region_code' => 'SG','url' => 'https://mws-fe.amazonservices.com/','site_url' => 'https://www.amazon.sg/','marketplace_id' => 'A19VAU5U5O7RUS','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '19','currency_id' => '8','region' => 'Australia','region_code' => 'AU','url' => 'https://mws.amazonservices.com.au/','site_url' => 'https://www.amazon.com.au/','marketplace_id' => 'A39IBJ37TRP1C6','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL),
            array('id' => '20','currency_id' => '4','region' => 'Japan','region_code' => 'JP','url' => 'https://mws.amazonservices.jp/','site_url' => 'https://www.amazon.co.jp/','marketplace_id' => 'A1VC38T7YXB528','status' => '1','created_at' => '2022-03-04 01:36:23','updated_at' => '2022-03-04 01:36:23','deleted_at' => NULL)
          );
          Region::insert($regions);
    }
}
