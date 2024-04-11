<?php

namespace CultuurNet\UDB3\JwtProvider\Console;

use CultuurNet\UDB3\Jwt\JwtEncoderServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncodeJwtCommand extends Command
{
    private JwtEncoderServiceInterface $encoder;

    public function __construct(JwtEncoderServiceInterface $encoder)
    {
        parent::__construct();
        $this->encoder = $encoder;
    }

    protected function configure(): void
    {
        $this
            ->setName('jwt:encode')
            ->setDescription('Encodes a JSON Web Token.')
            ->addArgument(
                'uid',
                InputArgument::REQUIRED,
                "User's id"
            )
            ->addArgument(
                'nick',
                InputArgument::REQUIRED,
                "User's nickname"
            )
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                "User's e-mail"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $claims = array_filter(
            $input->getArguments(),
            function ($claim): bool {
                return $claim !== 'command';
            },
            ARRAY_FILTER_USE_KEY
        );

        $token = $this->encoder->encode($claims);

        $output->writeln(['', $token]);

        return 0;
    }
}
