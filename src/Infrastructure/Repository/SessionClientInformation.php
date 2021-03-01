<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Repository;

use Aura\Session\Segment;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\ClientInformationRepositoryInterface;
use CultuurNet\UDB3\JwtProvider\Domain\Value\ClientInformation;

final class SessionClientInformation implements ClientInformationRepositoryInterface
{
    private const KEY = 'ClientInformation';

    /**
     * @var Segment
     */
    private $segment;

    public function __construct(Segment $segment)
    {
        $this->segment = $segment;
    }

    public function store(ClientInformation $clientInformation): void
    {
        $this->segment->set(
            self::KEY,
            [
                'value' => serialize($clientInformation),
            ]
        );
    }

    public function get(): ?ClientInformation
    {
        $values = $this->segment->get(self::KEY);

        if (!$values) {
            return null;
        }

        return unserialize($values['value']);
    }

    public function clear(): void
    {
        $this->segment->clear();
    }
}
