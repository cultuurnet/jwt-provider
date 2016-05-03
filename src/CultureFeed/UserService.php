<?php

namespace CultuurNet\UDB3\JwtProvider\CultureFeed;

use ValueObjects\String\String as StringLiteral;

class UserService implements UserServiceInterface
{
    /**
     * @var \ICultureFeed
     */
    private $cultureFeed;

    public function __construct(\ICultureFeed $cultureFeed)
    {
        $this->cultureFeed = $cultureFeed;
    }

    /**
     * @inheritdoc
     */
    public function getUser(StringLiteral $id)
    {
        return $this->cultureFeed->getUser($id);
    }
}