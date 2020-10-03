<?php

use Illuminate\Database\Seeder;

class MedicalServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\MedicalService::class, 10)->create();
    }
}
