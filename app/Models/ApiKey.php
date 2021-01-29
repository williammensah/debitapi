<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model{
    protected $table = 'api_key';

    protected $fillable = ['merchant_id','branch_id','email',];
}

