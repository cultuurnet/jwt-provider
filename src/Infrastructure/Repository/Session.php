<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Repository;

use Aura\Session\Segment;
use CultuurNet\UDB3\JwtProvider\Domain\Url;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;

class Session implements DestinationUrlRepository
{
    private const DESTINATION_URL = 'DestinationUrl';

    /**
     * @var Segment
     */
    private $sessionSegment;

    public function __construct(Segment $segment)
    {
        $this->sessionSegment = $segment;
    }

    public function storeDestinationUrl(Url $destinationUrl)
    {
        $this->sessionSegment->set(
            self::DESTINATION_URL,
            [
                'destination' => $destinationUrl->asString()
            ]
        );
    }

    public function getDestinationUrl(): ?Url
    {
        $values = $this->sessionSegment->get(self::DESTINATION_URL);

        if (!$values) {
            return null;
        }

        return Url::fromString($values['destination']);
    }

    public function removeDestinationUrl(): void
    {
        $this->sessionSegment->clear();
    }
}
