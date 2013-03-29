<?php

namespace Acme\Bundle\TestsBundle\Tests\Functional\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Acme\Bundle\TestsBundle\Test\ToolsAPI;

/**
 * @outputBuffering enabled
 */
class SoapUsersApiTest extends WebTestCase
{
    /** Default value for role label */
    const DEFAULT_VALUE = 'USER_LABEL';

    /** @var \SoapClient */
    public $client = null;

    public function setUp()
    {
        $this->clientSoap = static::createClient(array(), ToolsAPI::generateWsseHeader());
        $this->clientSoap->soap(
            "http://localhost/api/soap",
            array(
                'location' => 'http://localhost/api/soap',
                'soap_version' => SOAP_1_2
            )
        );
    }

    /**
     * @param string $request
     * @param array  $response
     *
     * @dataProvider requestsApi
     */
    public function testCreateUser($request, $response)
    {
        $result = $this->clientSoap->soapClient->createUser($request);
        $result = ToolsAPI::classToArray($result);
        ToolsAPI::assertEqualsResponse($response, $result, $this->clientSoap->soapClient->__getLastResponse());
    }

    /**
     * @param string $request
     * @param array  $response
     *
     * @dataProvider requestsApi
     * @depends testCreateUser
     */
    public function testUpdateUser($request, $response)
    {
        $this->markTestSkipped('Skipped due to BUG!!!');
        //get user id
        $userId = $this->clientSoap->soapClient->getUserBy(array('item' => array('key' =>'username', 'value' =>$request['username'])));
        $userId = ToolsAPI::classToArray($userId);

        $request['username'] .= '_Updated';
        $request['email'] .= '_Updated';
        unset($request['plainPassword']);
        $result = $this->clientSoap->soapClient->updateUser($userId['id'], $request);
        $result = ToolsAPI::classToArray($result);
        ToolsAPI::assertEqualsResponse($response, $result);
        $user = $this->clientSoap->soapClient->getUser($userId['id']);
        $user = ToolsAPI::classToArray($user);
        $this->assertEquals($request['username'], $user['username']);
        $this->assertEquals($request['email'], $user['email']);
    }

    /**
     * @dataProvider requestsApi
     * @depends testUpdateUser
     */
    public function testGetUsers($request, $response)
    {
        $users = $this->clientSoap->soapClient->getUsers(1, 1000);
        $users = ToolsAPI::classToArray($users);
        $result = false;
        foreach ($users as $user) {
            foreach ($user as $userDetails) {
                $result = $userDetails['username'] == $request['username'] . '_Updated';
                if ($result) {
                    break;
                }
            }
        }
        $this->assertTrue($result);
    }

    /**
     * @dataProvider requestsApi
     * @depends testGetUsers
     */
    public function testDeleteUser($request)
    {
        //get user id
        $userId = $this->clientSoap->soapClient->getUserBy(array('item' => array('key' =>'username', 'value' =>$request['username'] . '_Updated')));
        $userId = ToolsAPI::classToArray($userId);
        $result = $this->clientSoap->soapClient->deleteUser($userId['id']);
        $this->assertTrue($result);
        try {
            $this->clientSoap->soapClient->getUserBy(array('item' => array('key' =>'username', 'value' =>$request['username'] . '_Updated')));
        } catch (\SoapFault $e) {
            if ($e->faultcode != 'NOT_FOUND') {
                throw $e;
            }
        }
    }

    /**
     * Data provider for REST API tests
     *
     * @return array
     */
    public function requestsApi()
    {
        return ToolsAPI::requestsApi(__DIR__ . DIRECTORY_SEPARATOR . 'UserRequest');
    }
}
