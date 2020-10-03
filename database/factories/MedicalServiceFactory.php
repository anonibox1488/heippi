<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MedicalService;
use Faker\Generator as Faker;

$factory->define(MedicalService::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'description'=>$faker->sentence,
    ];
});
