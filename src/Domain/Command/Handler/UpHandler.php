<?php

namespace Fieg\Domain\Command\Handler;

use Fieg\Domain\Command\CommandInterface;
use Fieg\Domain\Command\Up;
use Fieg\Domain\Component\DependencySolver;
use Fieg\Domain\Doxfile\DoxfileLoaderInterface;
use Fieg\Domain\Provisioner\ProvisionerInterface;

class UpHandler implements HandlerInterface
{
    /**
     * @var DependencySolver
     */
    protected $solver;

    /**
     * @var DoxfileLoaderInterface
     */
    protected $doxfileLoader;

    /**
     * @var ProvisionerInterface
     */
    protected $provisioner;

    /**
     * @param DoxfileLoaderInterface $doxfileLoader
     * @param ProvisionerInterface $provisioner
     * @param DependencySolver $solver
     */
    public function __construct(
        DoxfileLoaderInterface $doxfileLoader,
        ProvisionerInterface $provisioner,
        DependencySolver $solver
    ) {
        $this->doxfileLoader = $doxfileLoader;
        $this->provisioner   = $provisioner;
        $this->solver        = $solver;
    }

    /**
     * @param CommandInterface|Up $command
     */
    public function handle(CommandInterface $command)
    {
        $doxfile = $this->doxfileLoader->load($command->getDoxfile());

        $groups = $doxfile->getGroups();

        foreach ($groups as $group) {
            $components = $this->solver->solve($group->getComponents());

            foreach ($components as $component) {
                if ($this->provisioner->supports($component)) {
                    $this->provisioner->provision($component);
                } else {
                    // @todo handle unsupported?
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function supports(CommandInterface $command)
    {
        return ($command instanceof Up);
    }
}
