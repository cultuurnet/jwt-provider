<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Domain\Service;

use CultuurNet\UDB3\ApiGuard\ApiKey\Reader\ApiKeyReaderInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerInterface;
use CultuurNet\UDB3\ApiGuard\Consumer\ConsumerReadRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Exception\InvalidApiKeyException;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;

class IsAllowedRefreshToken
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
    private $refreshGroupId;


    public function __construct(
        ConsumerReadRepositoryInterface $consumerReadRepository,
        ApiKeyReaderInterface $apiKeyReader,
        string $refreshGroupId
    ) {
        $this->consumerReadRepository = $consumerReadRepository;
        $this->apiKeyReader = $apiKeyReader;
        $this->refreshGroupId = $refreshGroupId;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @return bool
     * @throws InvalidApiKeyException
     * @throws InvalidArgumentException
     */
    public function __invoke(ServerRequestInterface $serverRequest): bool
    {
        $apiKey = $this->apiKeyReader->read($serverRequest);

        if ($apiKey == null) {
            throw new InvalidArgumentException();
        }

        $consumer = $this->consumerReadRepository->getConsumer($apiKey);

        if ($consumer === null) {
            throw new InvalidApiKeyException();
        }

        return $this->hasPermissionForRefresh($consumer);
    }

    private function hasPermissionForRefresh(ConsumerInterface $consumer): bool
    {
        foreach ($consumer->getPermissionGroupIds() as $group) {
            if ($group->toNative() === $this->refreshGroupId) {
                return true;
            }
        }

        return false;
    }
}
