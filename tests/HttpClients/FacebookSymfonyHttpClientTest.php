<?php

namespace Facebook\Tests\HttpClients;

use Facebook\HttpClients\FacebookSymfonyHttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class FacebookSymfonyHttpClientTest extends AbstractTestHttpClient
{
    /**
     * @requires PHP 7.1
     */
    public function testCanSendNormalRequest()
    {
        $fakeResponse = new MockResponse($this->fakeRawBody, ['http_code' => 200]);
        $mockHttpClient = new MockHttpClient($fakeResponse);
        $psrClient = new FacebookSymfonyHttpClient($mockHttpClient);
        $response = $psrClient->send('http://foo.com', 'GET', 'foo_body', [], 123);
        $this->assertInstanceOf('Facebook\Http\GraphRawResponse', $response);
        $this->assertEquals(200, $response->getHttpResponseCode());
        $this->assertEquals($this->fakeRawBody, $response->getBody());
    }
}
