<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model{
    protected $table = 'role';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = ['role_id','name','display_name','description'];
}
