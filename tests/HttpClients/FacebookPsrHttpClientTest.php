<?php

namespace Facebook\Tests\HttpClients;

use Facebook\Http\GraphRawResponse;
use Facebook\HttpClients\FacebookPsrHttpClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpClient\Response\MockResponse;

class FacebookPsrHttpClientTest extends AbstractTestHttpClient
{
    /** @var Psr17Factory */
    protected $psr17Factory;

    protected function setUp()
    {
        $this->psr17Factory = new Psr17Factory();
    }

    public function testCanSendNormalRequest()
    {
        $fakeResponse = new MockResponse($this->fakeRawBody, ['http_code' => 200]);
        $mockHttpClient = new MockHttpClient($fakeResponse);
        $psrClient = new FacebookPsrHttpClient(new Psr18Client($mockHttpClient), $this->psr17Factory, $this->psr17Factory);
        $response = $psrClient->send('http://foo.com', 'GET', 'foo_body', [], 123);
        $this->assertInstanceOf(GraphRawResponse::class, $response);
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertEquals($this->fakeRawBody, $response->getBody());
    }
}
