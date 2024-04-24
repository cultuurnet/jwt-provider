<?php

declare(strict_types=1);

namespace CultuurNet\UDB3\JwtProvider\Jwt;

use InvalidArgumentException;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

final class DefaultJwtDecoderService implements JwtDecoderService
{
    private Parser $parser;

    private ValidationData $validationData;

    private Signer $signer;

    private Key $publicKey;

    /**
     * @var string[]
     */
    private array $requiredClaims;

    public function __construct(
        Parser $parser,
        ValidationData $validationData,
        Signer $signer,
        Key $publicKey,
        array $requiredClaims = []
    ) {
        $this->parser = $parser;
        $this->validationData = $validationData;
        $this->signer = $signer;
        $this->publicKey = $publicKey;
        $this->requiredClaims = $requiredClaims;

        if (count($requiredClaims) !== count(array_filter($this->requiredClaims, 'is_string'))) {
            throw new InvalidArgumentException(
                "All required claims should be strings."
            );
        }
    }

    public function parse(string $tokenString): Token
    {
        try {
            return $this->parser->parse($tokenString);
        } catch (InvalidArgumentException $e) {
            throw new JwtParserException($e);
        }
    }

    public function validateData(Token $token): bool
    {
        return $token->validate($this->validationData);
    }

    public function validateRequiredClaims(Token $token): bool
    {
        foreach ($this->requiredClaims as $claim) {
            if (!$token->hasClaim($claim)) {
                return false;
            }
        }

        return true;
    }

    public function verifySignature(Token $token): bool
    {
        return $token->verify(
            $this->signer,
            $this->publicKey->getContent()
        );
    }
}