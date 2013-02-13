<?php

namespace Acme\Bundle\DemoSearchBundle\Tests\Functional\API;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

class SoapApiTest extends WebTestCase
{
    /** @var CustomSoapClient */
    static private $clientSoap = null;

    public function setUp()
    {
        if (is_null(self::$clientSoap)) {
            $client = static::createClient();
            //get wsdl
            $client->request('GET', 'api/soap');
            $wsdl = $client->getResponse()->getContent();
            self::$clientSoap = new CustomSoapClient($wsdl, array('location' =>'soap'), $client);
        }
    }

    /**
     * @param string $request
     * @param array $response
     *
     * @dataProvider requestsApi
     */
    public function testApi($request, $response)
    {
        if (is_null($request['search'])) {
            unset($request['search']);
        }
        if (is_null($request['offset'])) {
            unset($request['offset']);
        }
        if (is_null($request['max_results'])) {
            unset($request['max_results']);
        }
        $result = call_user_func_array(array(self::$clientSoap, 'search'), $request);
        $result = json_decode(json_encode($result), true);
        $this->assertEqualsResponse($response, $result);
    }

    /**
     * Data provider for REST API tests
     *
     * @return array
     */
    public function requestsApi()
    {
        $parameters = array();
        $testFiles = new RecursiveDirectoryIterator(
            __DIR__ . DIRECTORY_SEPARATOR . 'requests',
            RecursiveDirectoryIterator::SKIP_DOTS
        );
        foreach ($testFiles as $fileName => $object) {
            $parameters[$fileName] = Yaml::parse($fileName);
            if (is_null($parameters[$fileName]['response']['data'])) {
                unset($parameters[$fileName]['response']['data']);
            }
        }
        return
            $parameters;
    }

    /**
     * Test API response
     *
     * @param array $response
     * @param array $result
     */
    protected function assertEqualsResponse($response, $result)
    {
        $this->assertEquals($response['records_count'], $result['recordsCount']);
        $this->assertEquals($response['count'], $result['count']);
        if (isset($response['data']) && is_array($response['data'])) {
            foreach ($response['data'] as $key => $object) {
                foreach ($object as $property => $value) {
                    list($part1, $part2) = explode('_', $property);
                    $property = $part1 . ucfirst($part2);
                    if (isset($result['elements']['item'][0])) {
                        $this->assertEquals($value, $result['elements']['item'][$key][$property]);
                    } else {
                        $this->assertEquals($value, $result['elements']['item'][$property]);
                    }

                }
            }
        }
    }
}
