<?php

use Illuminate\Database\Seeder;
use Faker\Factory;

class PeopleSedder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        for ($i = 0; $i < 100; $i++) {
            DB::table('people')->insert([
                'firstName' => $faker->firstName,
                'lastName'  => $faker->lastName,
                'email'     => $faker->email
            ]);
        }
    }
}
