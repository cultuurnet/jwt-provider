<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Infrastructure\Repository;

use Aura\Session\Segment;
use CultuurNet\UDB3\JwtProvider\Domain\Repository\DestinationUrlRepository;
use Psr\Http\Message\UriInterface;
use Slim\Psr7\Factory\UriFactory;

class Session implements DestinationUrlRepository
{
    private const DESTINATION_URL = 'DestinationUrl';

    /**
     * @var Segment
     */
    private $sessionSegment;
    /**
     * @var UriFactory
     */
    private $uriFactory;

    public function __construct(Segment $segment, UriFactory $uriFactory)
    {
        $this->sessionSegment = $segment;
        $this->uriFactory = $uriFactory;
    }

    public function storeDestinationUrl(UriInterface $destinationUrl)
    {
        $this->sessionSegment->set(
            self::DESTINATION_URL,
            [
                'destination' => $destinationUrl->__toString()
            ]
        );
    }

    public function getDestinationUrl(): ?UriInterface
    {
        $values = $this->sessionSegment->get(self::DESTINATION_URL);

        if (!$values) {
            return null;
        }

        return $this->uriFactory->createUri($values['destination']);
    }

    public function removeDestinationUrl(): void
    {
        $this->sessionSegment->clear();
    }
}
