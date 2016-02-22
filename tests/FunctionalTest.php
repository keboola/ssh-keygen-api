<?php

namespace Keboola\SSH\Keygen\Tests;

use Silex\WebTestCase;
use Symfony\Component\HttpKernel\Client;

/**
 * Created by PhpStorm.
 * User: miroslavcillik
 * Date: 22/02/16
 * Time: 16:29
 */
class FunctionalTest extends WebTestCase
{
    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../index.php';
        $app['debug'] = true;
        unset($app['exception_handler']);

        return $app;
    }

    public function testPostKeys()
    {
        /** @var Client $client */
        $client = $this->createClient();
        $client->request('POST', 'keys');
        $response = $client->getResponse();

        $responseJson = json_decode($response->getContent(), true);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('private', $responseJson);
        $this->assertArrayHasKey('public', $responseJson);
    }
}
