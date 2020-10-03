<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Observation extends Model
{
    protected $primaryKey = 'id';

    protected $table ="observations";

    protected $fillable = [
        'observation', 'health_condition','specialty', 'patient_id', 'doctor_id'
    ];

    public function scopeUser($query, $userId){
    	$user = User::find($userId);
    	/*paciente*/
    	if ($user->type_id == 3) {
    		$query->where('patient_id',$user->id);
    	}
    	/*Medico*/
    	if ($user->type_id == 2) {
    		$query->where('doctor_id',$user->id);
    	}
    	/*Hospital*/
    	if ($user->type_id == 1) {
    		$ids = [];
    		$doctors = User::where('parent_id',$user->id)->get('id');
	    	foreach ($doctors as $value) {
	    		array_push($ids, $value->id);
	    	}
    		$query->whereIn('o.doctor_id',$ids);
    	}
    }
}