<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalService extends Model
{
    protected $primaryKey = 'id';

    protected $table ="medical_services";
    
    protected $fillable = [
        'name', 'description'
    ];
}
