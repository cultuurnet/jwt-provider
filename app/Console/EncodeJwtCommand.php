<?php

namespace CultuurNet\UDB3\JwtProvider\Console;

use CultuurNet\UDB3\Jwt\JwtEncoderServiceInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncodeJwtCommand extends AbstractCommand
{
    /**
     * @var string
     */
    private $encoderServiceName;

    /**
     * @param string $encoderServiceName
     *   Name of the JWT encoder service in the Silex application
     */
    public function __construct($encoderServiceName)
    {
        parent::__construct();
        $this->encoderServiceName = $encoderServiceName;
    }

    protected function configure()
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $claims = array_filter(
            $input->getArguments(),
            function ($claim) {
                return $claim !== 'command';
            },
            ARRAY_FILTER_USE_KEY
        );

        $token = $this->getEncoderService()
            ->encode($claims);

        $output->writeln(['', $token]);
    }

    /**
     * @return JwtEncoderServiceInterface
     */
    private function getEncoderService()
    {
        return $this->getService($this->encoderServiceName, JwtEncoderServiceInterface::class);
    }
}
