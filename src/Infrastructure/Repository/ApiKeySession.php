<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Repository;

use Aura\Session\Segment;
use CultuurNet\UDB3\ApiGuard\ApiKey\ApiKey;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ApiKeyRepositoryInterface;

class ApiKeySession implements ApiKeyRepositoryInterface
{
    const API_KEY = 'ApiKey';

    /**
     * @var Segment
     */
    private $segment;

    public function __construct(Segment $segment)
    {
        $this->segment = $segment;
    }

    public function storeApiKey(ApiKey $apiKey): void
    {
        $this->segment->set(
            self::API_KEY,
            [
                'apiKey' => $apiKey->__toString(),
            ]
        );
    }

    public function getApiKey(): ?ApiKey
    {
        $values = $this->segment->get(self::API_KEY);

        if (!$values) {
            return null;
        }

        return new ApiKey($values['apiKey']);
    }
}
