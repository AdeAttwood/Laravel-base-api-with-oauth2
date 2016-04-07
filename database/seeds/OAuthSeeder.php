<?php

use Illuminate\Database\Seeder;

class OAuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->insert([
            [
                'client_id'     => 'testclient',
                'client_secret' => 'testpass',
                'redirect_uri'  => 'http:/.localhost',
                'scope'         => 'people email comments'
            ], [
                'client_id'     => 'peopleclient',
                'client_secret' => 'testpass',
                'redirect_uri'  => 'http:/.localhost',
                'scope'         => 'people'
            ], [
                'client_id'     => 'emailclient',
                'client_secret' => 'testpass',
                'redirect_uri'  => 'http:/.localhost',
                'scope'         => 'email'
            ], [
                'client_id'     => 'peopleandcommentsclient',
                'client_secret' => 'testpass',
                'redirect_uri'  => 'http:/.localhost',
                'scope'         => 'people comments'
            ]
        ]);
    }
}
