<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branch';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [

        'merchant_id',
        'branch_name',
        'branch_code',
        'branch_location',
        'branch_email',
        'branch_phone_number',


    ];
    protected $primaryKey = 'branch_id';


    public function merchant()
    {
        return $this->belongsTo(Merchant::class,  'merchant_id');
    }
}
