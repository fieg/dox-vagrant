<?php

namespace Fieg\Domain\Provisioner;

use Fieg\Domain\Component\ComponentInterface;

interface ProvisionerInterface
{
    /**
     * @param ComponentInterface $component
     */
    public function provision(ComponentInterface $component);

    /**
     * @param ComponentInterface $component
     *
     * @return bool
     */
    public function supports(ComponentInterface $component);
}
