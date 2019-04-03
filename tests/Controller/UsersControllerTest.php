<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GuzzleHttp\Client as Client;

class UsersControllerTest extends WebTestCase
{
    private $token;
    private $entityManager;
    private $http;


    public function setUp()
    {
        $this->http = new Client(['base_uri' => '127.0.0.1:8000/v1/']);

        $this->setToken();
    }

    public function test_user_list()
    {
        $response = $this->http->request('GET', 'users', ['headers' => ['X-AUTH-TOKEN' => $this->token]]);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);
    }

    public function test_user_find()
    {
        $numberRnd = rand(0, 9999);

        $response = $this->http->request('POST', 'users', ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' => ['name' => 'TestName_'.$numberRnd,'lastname' => 'TestLatsName_'.$numberRnd,'email' => 'TestEmail_'.$numberRnd.'@server.com','roles' => '{"0":"ROLE_ADMIN"}','password' => 'passwordTest']]);

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);

        $response = $this->http->request('GET', 'users/'.$data['id'], ['headers' => ['X-AUTH-TOKEN' => $this->token]]);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);
    }

    public function test_user_create()
    {
        $numberRnd = rand(0, 9999);

        $response = $this->http->request('POST', 'users', ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' => ['name' => 'TestName_'.$numberRnd,'lastname' => 'TestLatsName_'.$numberRnd,'email' => 'TestEmail_'.$numberRnd.'@server.com','roles' => '{"0":"ROLE_ADMIN"}','password' => 'passwordTest']]);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_user_update()
    {
        $numberRnd = rand(0, 9999);

        $response = $this->http->request('POST', 'users', ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' => ['name' => 'TestName_'.$numberRnd,'lastname' => 'TestLatsName_'.$numberRnd,'email' => 'TestEmail_'.$numberRnd.'@server.com','roles' => '{"0":"ROLE_ADMIN"}' , 'password' => 'passwordTest']]);

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);

        $response = $this->http->request('PUT', 'users/'.$data['id'], ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' => ['name' => 'TestNameMod_'.$numberRnd,'lastname' => 'TestLatsNameMod_'.$numberRnd,'email' => 'TestEmailMod_'.$numberRnd.'@server.com','password' => 'passwordTestMod']]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_user_delete()
    {
        $numberRnd = rand(0, 9999);

        $response = $this->http->request('POST', 'users', ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' => ['name' => 'TestName_'.$numberRnd,'lastname' => 'TestLatsName_'.$numberRnd,'email' => 'TestEmail_'.$numberRnd.'@server.com','roles' => '{"0":"ROLE_ADMIN"}','password' => 'passwordTest']
        ]);

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);

        $response = $this->http->request('DELETE', 'users/'.$data['id'], ['headers' => ['X-AUTH-TOKEN' => $this->token]]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_user_search()
    {
        $response = $this->http->request('GET', 'users/search', ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' => ['param' => '{name:\'TestName_\'}','order' =>'{name:\'asc\'}' ]]);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);
    }

    private function setToken()
    {
        $response = $this->http->request('GET', 'login', ['query' => ['username' => 'internations@gmail.com','password' => 'hireme']]);

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);

        $this->token = $data['body'];
    }
}
