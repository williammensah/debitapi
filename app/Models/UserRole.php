<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model{

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $table = 'user_roles';
    protected $fillable = ['user_role_id','user_id','role_id',];
}

