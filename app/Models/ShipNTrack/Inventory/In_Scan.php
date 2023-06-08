<?php

namespace App\Models\ShipNtrack\Inventory;

use App\Models\ShipNtrack\Process\Process_Master;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class In_Scan extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $table = 'in_scans';
    protected $fillable = [
        'purchase_tracking_id',
        'manifest_id',
        'destination',
        'order_id',
        'awb_number',
       
    ];
    public function process()
    {
        return $this->hasOne(Process_Master::class, 'id', 'destination');
    }
}
