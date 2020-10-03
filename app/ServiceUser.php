<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceUser extends Model
{
    protected $primaryKey = 'id';

    protected $table ="services_users";

    protected $fillable = [
        'user_id', 'service_id'
    ];
}
