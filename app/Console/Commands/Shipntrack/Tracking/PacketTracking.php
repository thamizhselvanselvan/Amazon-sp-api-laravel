<?php

namespace App\Console\Commands\Shipntrack\Tracking;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\ShipNTrack\Packet\PacketForwarder;

class PacketTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mosh:shipntrack-packet-tracking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Packet Tracking untill getting targeted event';

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
        $PacketForwarder = PacketForwarder::where('status', NULL)
            ->orWhere('status', '')
            ->orWhere('status', '0')
            ->get();
    }
}
