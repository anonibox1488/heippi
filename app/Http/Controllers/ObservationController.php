<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ObservationStore;
use Maatwebsite\Excel\Facades\Excel;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Exports\ObservationExport;
use App\Observation;
use Carbon\Carbon;
use App\User;

class ObservationController extends Controller
{
    public function store(ObservationStore $request)
    {
    	try {
    		$observation = new Observation();
	    	$observation->observation = $request->observation;
	    	$observation->health_condition = $request->health_condition;
	    	$observation->specialty = $request->specialty;
	    	$observation->patient_id = $request->patient_id;
	    	$observation->doctor_id = JWTAuth::user()->id;
	    	
	    	if ($observation->save()) {
	    		return response()->json([
		    		'resp'=>'Observacion Registrada con Exito',
		    		'data'=>$observation
		    	], 200);
	    	}
    		
    	} catch (Exception $e) {
    		return [
	    		'resp'=>'Error inesperado inténtelo de nuevo',
	    		'data'=>$e,
	    		'code'=>500,
	    	];
    	}
    }

    public function getObservations()
    { 
    	$observations = Observation::from('observations as o')
        ->select('o.specialty', 'o.observation', 'o.health_condition','p.name as paciente', 'd.name as medico', 'h.name as hospital') 
        ->join('users as p','o.patient_id','p.id')
        ->join('users as d','o.doctor_id','d.id')
        ->join('users as h','d.parent_id','h.id')
        ->user(JWTAuth::user()->id)
        ->get(); 
        return response()->json([
    		'resp'=>'Observaciones',
    		'data'=>$observations
    	], 200);
    }
    
	public function getXlsObservations()
    { 	
		// return (new ObservationExport(JWTAuth::user()->id))->download('Observaciones.xlsx');
		return Excel::download(new ObservationExport(1), 'Observaciones.xlsx');
    }
}
