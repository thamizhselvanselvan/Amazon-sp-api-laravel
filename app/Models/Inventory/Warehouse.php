<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    use HasFactory;
    protected $connection = 'in';

    protected $fillable = ['name',
                           'address_1',
                           'address_2',
                           'city',
                           'state',
                           'country',
                           'pin_code',
                           'contact_person_name',
                           'phone_number' ,
                           'email',];
}
