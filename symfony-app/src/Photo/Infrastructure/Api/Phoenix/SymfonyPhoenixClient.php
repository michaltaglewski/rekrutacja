<?php

declare(strict_types=1);

namespace App\Photo\Infrastructure\Api\Phoenix;

use App\Photo\Application\Api\Phoenix\PhoenixClient;
use App\Photo\Application\Api\Phoenix\Response\PhotosCollection;
use App\Photo\Application\Exception\PhoenixApiClientHttpException;
use App\Photo\Application\Exception\PhoenixApiUnauthorizedHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SymfonyPhoenixClient implements PhoenixClient
{
    private const PHOTOS_PATH = '/api/photos';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly UriResolver $uriResolver,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getPhotos(string $accessToken): PhotosCollection
    {
        $url = $this->uriResolver->getUri(self::PHOTOS_PATH);

        try {
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'access-token' => $accessToken
                ]
            ]);
        } catch (ClientExceptionInterface $exception) {
            throw new PhoenixApiClientHttpException($exception->getMessage(), previous: $exception);
        }

        if ($response->getStatusCode() === 401) {
            throw new PhoenixApiUnauthorizedHttpException(
                'Unauthorized request: ' . $response->getContent(false),
            );
        }

        if ($response->getStatusCode() >= 500) {
            throw new PhoenixApiClientHttpException(
                'Error while sending getService request: ' . $response->getContent(false),
            );
        }

        return $this->deserialize($response, PhotosCollection::class);
    }

    private function deserialize(ResponseInterface $response, string $class): object
    {
        return $this->serializer->deserialize(
            $response->getContent(),
            $class,
            'json'
        );
    }
}
