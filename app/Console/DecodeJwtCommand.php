<?php

namespace CultuurNet\UDB3\JwtProvider\Console;

use CultuurNet\UDB3\JwtProvider\Jwt\JwtDecoderService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DecodeJwtCommand extends Command
{
    private JwtDecoderService $decoder;

    public function __construct(JwtDecoderService $decoder)
    {
        parent::__construct();
        $this->decoder = $decoder;
    }

    protected function configure(): void
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $token = $this->decoder->parse($input->getArgument('token'));

        $output->writeln('');

        foreach ($token->getClaims() as $claim => $value) {
            $output->writeln("{$claim}: {$value}");
        }

        $valid = $this->decoder->validateData($token);
        $verified = $this->decoder->verifySignature($token);

        $output->writeln('Valid: ' . ($valid ? '✓' : '✕'));
        $output->writeln('Signature verification: ' . ($verified ? '✓' : '✕'));

        // Return 0 as exit code if verified & valid, otherwise 1.
        return (int)!($valid && $verified);
    }
}
