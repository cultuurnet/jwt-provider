<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Exception\UnSuccessfulRefreshException;
use CultuurNet\UDB3\JwtProvider\Domain\Service\RefreshServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class RefreshAuth0Adapter implements RefreshServiceInterface
{
    /** @var Client */
    private $httpClient;

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var string */
    private $domain;

    public function __construct(Client $client, string $clientId, string $clientSecret, string $domain)
    {
        $this->httpClient = $client;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->domain = $domain;
    }

    /**
     * @inheritDoc
     */
    public function token(string $refreshToken): string
    {
        try {
            $response = $this->httpClient->post(
                $this->uri(),
                [
                    'headers' => ['content-type' => 'application/x-www-form-urlencoded'],
                    'body' => $this->body($refreshToken),
                ]
            );
            $res = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            return $res['id_token'];
        } catch (\JsonException|GuzzleException $exception) {
            throw new UnSuccessfulRefreshException($exception->getMessage());
        }
    }

    private function body(string $refreshToken): string
    {
        return sprintf(
            'grant_type=refresh_token&client_id=%s&client_secret=%s&refresh_token=%s',
            $this->clientId,
            $this->clientSecret,
            $refreshToken
        );
    }

    private function uri(): string
    {
        return sprintf('https://%s/oauth/token', $this->domain);
    }
}
