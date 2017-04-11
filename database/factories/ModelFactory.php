<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */


$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'firstname' => $faker->firstname,
        'name' => $faker->name,
        'username' => $faker->userName,
        'address' => $faker->address,
        'town' => $faker->city,
        'postcode'=> intval($faker->postcode),
        'country' => $faker->country,
        'activity' => 'curiosity',
        'email' => $faker->unique()->safeEmail,
        'password' => bcrypt($faker->password),
        'log_id' => str_random(64),
        'admin' => 0,
        //$password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});
