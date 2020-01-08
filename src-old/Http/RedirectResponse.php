<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Http;

use Fig\Http\Message\StatusCodeInterface;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

final class RedirectResponse extends Response
{
    public function __construct(string $location, int $status = StatusCodeInterface::STATUS_MOVED_PERMANENTLY)
    {
        $headers = new Headers(['Location' => $location]);
        parent::__construct($status, $headers);
    }
}
