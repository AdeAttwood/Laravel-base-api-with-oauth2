<?php


use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use GuzzleHttp\Client;

class OAuthTest extends TestCase
{

    /**
     * test geting a access token
     *
     * @return void
     */
    public function testGetToken()
    {
        $client = new Client();

        $body['grant_type'] = "client_credentials";
        $user = 'testclient';
        $pass = 'testpass';
        $res = $client->post($this->baseUrl . '/token', [ 
           'form_params' => $body,
           'auth' => [$user, $pass] 
        ]);

        $code = $res->getStatusCode();
        $body = $res->getBody();

        $this->assertEquals(200, $code);
    }
}
