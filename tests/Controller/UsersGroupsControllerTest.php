<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GuzzleHttp\Client as Client;

class UsersGroupsControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->http = new Client(['base_uri' => '127.0.0.1:8000/v1/']);

        $this->setToken();
    }

    public function test_user_group_list()
    {
        $response = $this->http->request('GET', 'user/groups', ['headers' => ['X-AUTH-TOKEN' => $this->token]]);

        $this->assertEquals(200, $response->getStatusCode());

        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json", $contentType);
    }

    public function test_user_group_create()
    {
        $numberRnd = rand(0, 9999);

        $response = $this->http->request('POST', 'users', ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' => ['name' => 'TestName_'.$numberRnd,'lastname' => 'TestLatsName_'.$numberRnd,'email' => 'TestEmail_'.$numberRnd.'@server.com','password' => 'passwordTest']]);

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);

        $userId = $data['id'];

        $numberRnd = rand(0, 9999);

        $response = $this->http->request('POST', 'groups', ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' => ['name' => 'TestGrpName_'.$numberRnd,'description' => 'TestGrpDescription_'.$numberRnd]]);

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);

        $groupId = $data['id'];

        $response = $this->http->request('POST', 'user/groups', ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' =>['userId' => $userId, 'groupId' => $groupId]]);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test_user_group_delete()
    {
        $numberRnd = rand(0, 9999);

        $response = $this->http->request('POST', 'users', ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' => ['name' => 'TestName_'.$numberRnd,'lastname' => 'TestLatsName_'.$numberRnd,'email' => 'TestEmail_'.$numberRnd.'@server.com','password' => 'passwordTest']]);

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);

        $userId = $data['id'];

        $numberRnd = rand(0, 9999);

        $response = $this->http->request('POST', 'groups', ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' => ['name' => 'TestGrpName_'.$numberRnd,'description' => 'TestGrpDescription_'.$numberRnd]]);

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);

        $groupId = $data['id'];

        $response = $this->http->request('POST', 'user/groups', ['headers' => ['X-AUTH-TOKEN' => $this->token],'query' =>['userId' => $userId, 'groupId' => $groupId]]);

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);

        $userGroupId = $data['id'];

        $response = $this->http->request('DELETE', 'user/groups/'.$userGroupId, ['headers' => ['X-AUTH-TOKEN' => $this->token]]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    private function setToken()
    {
        $response = $this->http->request('GET', 'login', ['query' => ['username' => 'internations@gmail.com','password' => 'hireme']]);

        $body = $response->getBody();
        $data = json_decode($body->getContents(), true);

        $this->token = $data['body'];
    }
}
