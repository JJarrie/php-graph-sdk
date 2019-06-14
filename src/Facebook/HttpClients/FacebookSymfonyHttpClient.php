<?php

namespace Facebook\HttpClients;

use Facebook\Exceptions\FacebookSDKException;
use Facebook\Http\GraphRawResponse;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FacebookSymfonyHttpClient implements FacebookHttpClientInterface
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient = null)
    {
        if (null === $httpClient && !class_exists('Symfony\Component\HttpClient\HttpClient')) {
            throw new \LogicException('You cannot use the "Facebook\HttpClients\FacebookSymfonyHttpClient" as no class implementing "Symfony\Contracts\HttpClient\HttpClientInterface" has been provided. Try running "composer require symfony/http-client".');
        }

        $this->httpClient = $httpClient ?: HttpClient::create();
    }

    public function send($url, $method, $body, array $headers, $timeOut)
    {
        try {
            $response = $this->httpClient->request($method, $url, ['headers' => $headers, 'timeout' => $timeOut]);
        } catch (TransportException $e) {
            throw new FacebookSDKException($e->getMessage(), $e->getCode());
        }

        return new GraphRawResponse($response->getHeaders(), $response->getContent(), $response->getStatusCode());
    }
}
