<?php

namespace App\Console\Commands\Shipntrack\Tracking;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use App\Models\ShipNTrack\Courier\CourierPartner;

class GenerateSPLToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:generate-spl-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to generate Saudi Post ltd Courier partner API Tocken genertion. ';

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
       // Get the token from the API response
       
       $cred = CourierPartner::with(['courier_names'])
                ->whereHas('courier_names', function ($query) {
                    $query->where('courier_name', 'SPL');
                })
                ->first();      

       $username = $cred['user_name'];
       $password = $cred['password'];

       $URL =  'https://dev.gnteq.app/api/identity/Authentication/GetToken' ; 

       $creds =   [
                       "username"    =>  $username,
                       "password"     =>  $password
                   ];

       $response = Http::withoutVerifying()
                           ->withHeaders([
                               'Content-Type' => 'application/json',
                               'Accept' => 'application/json',
                           ])
                           ->post($URL, $creds);


        
        $token = $response['token']['access_token'];
       

       // Create the directory if it doesn't exist
       $directory = storage_path('app/business');
       File::makeDirectory($directory, 0755, true, true);

       // Store the token in the text file
       $filePath = $directory . '/SPLToken.txt';
       File::put($filePath, $token);

       $this->info('Token generated successfully.');
    }
       
}
