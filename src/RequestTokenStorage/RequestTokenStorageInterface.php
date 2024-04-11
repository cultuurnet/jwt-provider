<?php

namespace CultuurNet\UDB3\JwtProvider\RequestTokenStorage;

use CultuurNet\Auth\TokenCredentials as RequestToken;

interface RequestTokenStorageInterface
{
    public function storeRequestToken(RequestToken $requestToken): void;

    public function getStoredRequestToken(): ?RequestToken;

    public function removeStoredRequestToken(): void;
}
