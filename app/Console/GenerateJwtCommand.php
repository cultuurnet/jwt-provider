<?php

namespace CultuurNet\UDB3\JwtProvider\Console;

use Knp\Command\Command;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateJwtCommand extends Command
{
    /**
     * @var string
     */
    private $builderServiceName;

    /**
     * @var string
     */
    private $signerServiceName;

    /**
     * @var string
     */
    private $keyName;

    /**
     * @var string
     */
    private $iss;

    /**
     * @var int
     */
    private $exp;

    /**
     * @var int
     */
    private $nbf;

    /**
     * @param string $builderServiceName
     *   Name of the Builder service in the Silex application
     * @param string $signerServiceName
     *   Name of the Signer service in the Silex application
     * @param string $keyName
     *   Name of the Key in the Silex application
     * @param string $iss
     *   Value for the iss claim.
     * @param int $exp
     *   Value for the exp claim (after current time).
     * @param int $nbf
     *   Value for the nbf claim (after current time).
     */
    public function __construct(
        $builderServiceName,
        $signerServiceName,
        $keyName,
        $iss,
        $exp,
        $nbf = 0
    ) {
        parent::__construct();

        $this->guardType($iss, 'string');
        $this->guardType($exp, 'integer');
        $this->guardType($nbf, 'integer');

        $this->builderServiceName = $builderServiceName;
        $this->signerServiceName = $signerServiceName;
        $this->keyName = $keyName;
        $this->iss = $iss;
        $this->exp = $exp;
        $this->nbf = $nbf;
    }

    protected function configure()
    {
        $this
            ->setName('jwt:generate')
            ->setDescription('Generates a JSON Web Token.')
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
            )
            ->addArgument(
                'token',
                InputArgument::REQUIRED,
                "User's token"
            )->addArgument(
                'secret',
                InputArgument::REQUIRED,
                "User's secret"
            );

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $builder = $this->getBuilder();
        foreach ($input->getArguments() as $claim => $value) {
            $builder = $builder->set($claim, $value);
        }

        $token = $builder
            ->setIssuer($this->iss)
            ->setExpiration(time() + $this->exp)
            ->setNotBefore(time() + $this->nbf)
            ->sign(
                $this->getSigner(),
                $this->getKey()
            )
            ->getToken();

        $output->writeln(["JSON Web Token:", $token]);
    }

    /**
     * @return Builder
     */
    private function getBuilder()
    {
        return $this->getService($this->builderServiceName, Builder::class);
    }

    /**
     * @return Signer
     */
    private function getSigner()
    {
        return $this->getService($this->signerServiceName, Signer::class);
    }

    /**
     * @return Key
     */
    private function getKey()
    {
        return $this->getService($this->keyName, Key::class);
    }

    /**
     * @param string $name
     * @param string $expectedType
     */
    private function getService($name, $expectedType)
    {
        $app = $this->getSilexApplication();
        $service = $app[$name];

        if (!($service instanceof $expectedType)) {
            throw new \RuntimeException("{$name} is not of type {$expectedType}.");
        }

        return $service;
    }

    /**
     * @param mixed $var
     * @param string $type
     */
    private function guardType($var, $type)
    {
        $actualType = gettype($var);

        if ($type !== $actualType) {
            throw new \InvalidArgumentException(
                sprintf(
                    'iss should be of type %s, %s given.',
                    $type,
                    $actualType
                )
            );
        }
    }
}
