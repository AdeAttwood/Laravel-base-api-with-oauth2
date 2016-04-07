<?php


use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use GuzzleHttp\Client;

class PeopleTest extends TestCase
{

    public static function setUpBeforeClass()
    {
        TestCase::setTokens();
    }

    /**
     * A stander api call only asking for a accsess_token
     *
     * @param string $access_token token to gain accsess to the api
     * @param array  $queryParams  addisanal paramiter to send in the api
     * 
     * @return \GuzzleHttp\Psr7\Response
     */
    public function standerdApiCall($access_token, $queryParams = [])
    {
        $query =  [
            'access_token' => $access_token
        ];

        try {
            $client = new Client();
            return $client->get($this->baseUrl . '/people',[
                'query' => $query + $queryParams 
            ]);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            return $e->getResponse();
        } catch(GuzzleHttp\Exception\ServerException $e) {
            return $e->getResponse();
        }

    }

    /**
     * A basic test example.
     */
    public function testStanderdCall()
    {
        $client = new Client();
        $res = $client->get($this->baseUrl . '/people',[
            'query' => [ 
                'access_token' => TestCase::$testclient_token
            ]
        ]);
        $responce = json_decode((string)$res->getBody());
        $this->assertEquals($responce->current_page, 1);
    }

    /**
     * test tabel auth useing client scope 
     *
     */
    public function testTabelAuth()
    {
        $res_people = $this->standerdApiCall(TestCase::$peopleclient_token);
        $res_email  = $this->standerdApiCall(TestCase::$emailclient_token);

        $res_people_body = json_decode((string)$res_people->getBody());
        $res_email_body  = json_decode((string)$res_email->getBody());

        $this->assertEquals($res_people->getStatusCode(), 200);
        $this->assertEquals($res_email->getStatusCode(), 401);

        $this->assertEquals($res_email_body->error, 'insufficient_scope');
        $this->assertArrayHasKey('data', (array)$res_people_body);
    }

    /**
     * test auth on a relation ship of the table
     *
     */
    public function testRelationshipAuth()
    {
        $res_people = $this->standerdApiCall(TestCase::$peopleclient_token);
        $res_peopleAndComments = $this->standerdApiCall(TestCase::$peopleandcommentsclient_token);
        $res_people_body = json_decode((string)$res_people->getBody());
        $res_peopleAndComments_body = json_decode((string)$res_peopleAndComments->getBody());

        $this->assertObjectHasAttribute('comments', $res_peopleAndComments_body->data->{24});
        $this->assertObjectNotHasAttribute('comments', $res_people_body->data->{24});
    }

    /**
     * test the "return" in the query string only return the correct fields from the database
     *
     */
    public function testReturnOnlySomeParmasAndAuth()
    {
        $res_people = $this->standerdApiCall(TestCase::$peopleclient_token, [
            'return'=> 'firstName,lastName'
        ]);
        $res_peopleAndComments = $this->standerdApiCall(TestCase::$peopleandcommentsclient_token, [
          'return' => 'firstName,lastName,comments-userID,comments-comment'  
        ]);
        $res_people_fail = $this->standerdApiCall(TestCase::$peopleclient_token, [
            'return'=> 'firstName,lastName,comments-userID'
        ]);
        
        $res_people_body = json_decode((string)$res_people->getBody());
        $res_peopleAndComments_body = json_decode((string)$res_peopleAndComments->getBody());
        $res_people_fail_body = json_decode((string)$res_people_fail->getBody());

        // assert $res_people only has first name and and lastName
        $this->assertObjectNotHasAttribute('id', $res_people_body->data->{20});
        $this->assertObjectNotHasAttribute('email', $res_people_body->data->{20});
        $this->assertObjectNotHasAttribute('comments', $res_people_body->data->{20});
        
        $this->assertObjectHasAttribute('firstName', $res_people_body->data->{20});
        $this->assertObjectHasAttribute('lastName', $res_people_body->data->{20});

        // assert $res_peopleAndComments only has firstName lastName commentUserID
        $this->assertObjectNotHasAttribute('id', $res_peopleAndComments_body->data->{20});
        $this->assertObjectNotHasAttribute('email', $res_peopleAndComments_body->data->{20});
        
        $this->assertObjectHasAttribute('firstName', $res_peopleAndComments_body->data->{20});
        $this->assertObjectHasAttribute('lastName', $res_peopleAndComments_body->data->{20});
        if (isset($res_peopleAndComments_body->data->{20}->comments)) {
            $this->assertObjectHasAttribute('userID', $res_peopleAndComments_body->data->{20}->comments[0]);
            $this->assertObjectHasAttribute('comment', $res_peopleAndComments_body->data->{20}->comments[0]);
            
            $this->assertObjectNotHasAttribute('id', $res_peopleAndComments_body->data->{20}->comments[0]);
        } else {
             $this->fail('comments dose not exist in $res_peopleAndComments_body');
        }
        // assert $people_fail has error message and 401 respopne code
        $this->assertEquals($res_people_fail->getStatusCode(), 401);
        $this->assertEquals($res_people_fail_body->error, 'insufficient_scope');
    }
}
