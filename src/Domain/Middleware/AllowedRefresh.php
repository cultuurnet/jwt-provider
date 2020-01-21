<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Middleware;

use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\InvalidApiKeyException;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\RefreshTokenNotAllowedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AllowedRefresh implements MiddlewareInterface
{
    /**
     * @var ConsumerReadRepositoryInterface
     */
    private $consumerReadRepository;

    /**
     * @var ApiKeyReaderInterface
     */
    private $apiKeyReader;

    /**
     * @var string
     */
    private $refreshPermissionGroup;

    public function __construct(
        ConsumerReadRepositoryInterface $consumerReadRepository,
        ApiKeyReaderInterface $apiKeyReader,
        string $refreshPermissionGroup
    ) {
        $this->consumerReadRepository = $consumerReadRepository;
        $this->apiKeyReader = $apiKeyReader;
        $this->refreshPermissionGroup = $refreshPermissionGroup;
    }


    /**
     * @inheritDoc
     * @throws InvalidApiKeyException
     * @throws RefreshTokenNotAllowedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $apiKey = $this->apiKeyReader->read($request);

        if ($apiKey === null) {
            return $handler->handle($request);
        }

        $consumer = $this->consumerReadRepository->getConsumer($apiKey);

        if ($consumer === null) {
            throw new InvalidApiKeyException();
        }

        if (!$this->hasPermissionForRefresh($consumer)) {
            throw new RefreshTokenNotAllowedException();
        }

        return $handler->handle($request);
    }

    private function hasPermissionForRefresh(ConsumerInterface $consumer): bool
    {
        foreach ($consumer->getPermissionGroupIds() as $group) {
            if ($group->toNative() === $this->refreshPermissionGroup) {
                return true;
            }
        }

        return false;
    }
}
