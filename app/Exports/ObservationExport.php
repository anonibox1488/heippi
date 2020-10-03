<?php

namespace App\Exports;

use App\Observation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ObservationExport implements FromView
{

	public function __construct($id)
    {
        $this->userid = $id;
    }
    

    public function view(): View
    {
    	$observations = Observation::from('observations as o')
		->select('o.specialty', 'o.observation', 'o.health_condition','p.name as paciente', 'd.name as medico', 'h.name as hospital') 
		->join('users as p','o.patient_id','p.id')
		->join('users as d','o.doctor_id','d.id')
		->join('users as h','d.parent_id','h.id')
		->user($this->userid)
		->get();
        return view('reports.Observations', [
            'observations' =>  $observations
        ]);
    }
}
