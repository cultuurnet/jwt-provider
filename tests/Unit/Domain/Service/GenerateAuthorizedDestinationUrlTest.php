<?php declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Unit\Domain\Service;

use CultuurNet\UDB3\JwtProvider\Domain\Url;
use CultuurNet\UDB3\JwtProvider\Domain\Service\GenerateAuthorizedDestinationUrl;
use PHPUnit\Framework\TestCase;

class GenerateAuthorizedDestinationUrlTest extends TestCase
{

    /**
     * @test
     */
    public function it_appends_token_to_query_params_list()
    {
        $destinationUrl = Url::fromString('http://bar.com/?query=value');
        $generateAuthorizedDestinationUrlTest = new GenerateAuthorizedDestinationUrl();
        $result = $generateAuthorizedDestinationUrlTest->__invoke($destinationUrl,'token');

        $this->assertEquals($result->asString(),'http://bar.com/?query=value&jwt=token');
    }

    /**
     * @test
     */
    public function it_adds_token_as_query_param()
    {
        $destinationUrl = Url::fromString('http://bar.com/');
        $generateAuthorizedDestinationUrlTest = new GenerateAuthorizedDestinationUrl();
        $result = $generateAuthorizedDestinationUrlTest->__invoke($destinationUrl,'token');

        $this->assertEquals($result->asString(),'http://bar.com/?jwt=token');
    }
}
