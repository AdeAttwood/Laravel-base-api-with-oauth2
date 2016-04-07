<?php

use Illuminate\Database\Seeder;
use Faker\Factory;

class CommentSeeder extends Seeder
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
            DB::table('comments')->insert([
                'userID' => $faker->numberBetween(1, 100),
                'comment'  => $faker->text()
            ]);
        }

    }
}
