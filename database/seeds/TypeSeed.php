<?php

use Illuminate\Database\Seeder;

class TypeSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
			['name'=>'Hospital'],
			['name'=>'Médico'],
			['name'=>'Paciente']
        ];
        DB::table('types')->insert($data);
    }
}
