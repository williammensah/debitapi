<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model{
    protected $table = 'merchant';
    protected $primaryKey = 'merchant_id';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable=[
        'transflow_id',
         'merchant_name',
         'email',
         'phone_number',

    ];
    

}
