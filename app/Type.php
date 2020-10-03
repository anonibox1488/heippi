<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $primaryKey = 'id';

    protected $table ="types";

    protected $fillable = [
        'name',
    ];
}
