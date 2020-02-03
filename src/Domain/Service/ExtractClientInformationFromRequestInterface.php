<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Value\ClientInformation;
use Psr\Http\Message\ServerRequestInterface;

interface ExtractClientInformationFromRequestInterface
{
    public function __invoke(ServerRequestInterface $serverRequest): ClientInformation;
}
