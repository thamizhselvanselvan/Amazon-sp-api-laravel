<?php

namespace App\Models\Admin\BB;

use App\Models\Aws_credential;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BB_User extends Model
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;
    protected $connection = 'buybox';
    protected $table ='users';
    
    protected $fillable = [
        'internal_seller',
        'name',
        'email',
        'google_id',
        'password',
        'status',
        'ip_address',
        'user_agent',
        'seller_id',
        'is_manager',
    ];

    public function aws_credentials() {
        return $this->hasOne(Aws_credential::class, 'seller_id', 'id');
    }
}
