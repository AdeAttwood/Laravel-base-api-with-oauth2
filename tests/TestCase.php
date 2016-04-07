<?php

use GuzzleHttp\Client;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * token for the testclient user
     * 
     * @var string
     */
    protected static $testclient_token;


    /**
     * token for the emailclient user
     * 
     * @var string
     */
    protected static $emailclient_token;

    /**
     * token for the peopleclient user
     * 
     * @var string
     */
    protected static $peopleclient_token;

    /**
     * token for the peopoleandcommentsclient user
     * 
     * @var string
     */
    protected static $peopleandcommentsclient_token;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public static function setTokens()
    {
        TestCase::$testclient_token    = Self::getToken('testclient', 'testpass');
        TestCase::$emailclient_token   = Self::getToken('emailclient', 'testpass');
        TestCase::$peopleclient_token  = Self::getToken('peopleclient', 'testpass');
        TestCase::$peopleandcommentsclient_token = Self::getToken('peopleandcommentsclient', 'testpass');
    }

    /**
     * make a call to the api and get a access_token
     *
     * @param string $username
     * @param string $password
     *
     * @return string access_token for the api
     */
    private static function getToken($username, $password)
    {
        $client = new Client();
        $res = $client->post('http://localhost' . '/token', [ 
            'form_params' =>[
                'grant_type' => 'client_credentials'
            ], 
            'auth' => [$username, $password] 
        ]);
        $body = $res->getBody();
        return json_decode((string)$body)->access_token;
    }
}
