<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(PeopleSedder::class);
         $this->call(CommentSeeder::class);
         $this->call(OAuthSeeder::class);
    }
}
