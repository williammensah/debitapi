<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiClientService extends Model{
    protected $table = 'api_clients_services';

    protected $fillable =['merchant_id','client_name','client_key'];

    public $timestamps = false;

}
