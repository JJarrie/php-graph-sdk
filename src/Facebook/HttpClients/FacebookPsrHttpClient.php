<?php

namespace Facebook\HttpClients;

use Facebook\Http\GraphRawResponse;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\HttpClient\Psr18Client;

class FacebookPsrHttpClient implements FacebookHttpClientInterface
{
    private $httpClient;
    private $requestFactory;
    private $streamFactory;

    public function __construct(ClientInterface $httpClient = null, RequestFactoryInterface $requestFactory = null, StreamFactoryInterface $streamFactory = null)
    {
        if (null === $httpClient && !class_exists(Psr18Client::class)) {
            throw new \LogicException('You cannot use the "Facebook\HttpClients\FacebookPsrHttpClient" as no PSR-18 client have been provided. Try running "composer require symfony/http-client".');
        }

        $this->httpClient = $httpClient ?: new Psr18Client();

        if ((null === $requestFactory || null === $streamFactory) && !class_exists(Psr17Factory::class)) {
            throw new \LogicException('You cannot use the "Facebook\HttpClients\FacebookPsrHttpClient" as no PSR-17 factories have been provided. Try running "composer require nyholm/psr7".');
        }

        $this->requestFactory = $requestFactory ?: new Psr17Factory();
        $this->streamFactory = $streamFactory ?: new Psr17Factory();
    }

    public function send($url, $method, $body, array $headers, $timeOut)
    {
        $request = $this->requestFactory->createRequest($method, $url);
        foreach ($headers as $name => $value) {
            $request->withHeader($name, $value);
        }
        $request->withBody($this->streamFactory->createStream($body));

        // TODO: Do something with timeout 

        $response = $this->httpClient->sendRequest($request);

        return new GraphRawResponse($response->getHeaders(), $response->getBody(), $response->getStatusCode());
    }

}