<?php

namespace CultuurNet\UDB3\JwtProvider\Console;

use CultuurNet\UDB3\Jwt\JWTDecoderServiceInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ValueObjects\StringLiteral\StringLiteral;

class DecodeJwtCommand extends AbstractCommand
{
    /**
     * @var string
     */
    private $decoderServiceName;

    /**
     * @param string $decoderServiceName
     */
    public function __construct(
        $decoderServiceName
    ) {
        parent::__construct();
        $this->decoderServiceName = $decoderServiceName;
    }

    protected function configure()
    {
        $this
            ->setName('jwt:decode')
            ->setDescription('Decodes, validates, and verifies a JSON Web Token.')
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                "JWT to decode"
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $decoder = $this->getDecoderService();

        $token = $decoder->parse(
            new StringLiteral($input->getArgument('token'))
        );

        $output->writeln('');

        foreach ($token->getClaims() as $claim => $value) {
            $output->writeln("{$claim}: {$value}");
        }

        $valid = $decoder->validateData($token);
        $verified = $decoder->verifySignature($token);

        $output->writeln('Valid: ' . ($valid ? '✓' : '✕'));
        $output->writeln('Signature verification: ' . ($verified ? '✓' : '✕'));

        // Return 0 as exit code if verified & valid, otherwise 1.
        return !($valid && $verified);
    }

    /**
     * @return JWTDecoderServiceInterface
     */
    private function getDecoderService()
    {
        return $this->getService($this->decoderServiceName, JWTDecoderServiceInterface::class);
    }
}
