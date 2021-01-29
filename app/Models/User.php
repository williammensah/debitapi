<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'merchant_id',
        'branch_id',
        'username',
        'name',
        'email',
        'phone_number',
        'password',
    ];



    public function merchant()
    {
        return $this->belongsTo(Merchant::class,  'merchant_id');
    }
}
