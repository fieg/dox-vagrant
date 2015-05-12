<?php

namespace Fieg\Domain\Provisioner;

use Fieg\Domain\Component\ComponentInterface;

class DelegatingProvisioner implements ProvisionerInterface
{
    /**
     * @var ProvisionerInterface[]
     */
    protected $provisioners = [];

    /**
     * @param ProvisionerInterface $provisioner
     */
    public function addProvisioner(ProvisionerInterface $provisioner)
    {
        $this->provisioners[] = $provisioner;
    }

    /**
     * @param ComponentInterface $component
     */
    public function provision(ComponentInterface $component)
    {
        foreach ($this->provisioners as $provisioner) {
            if ($provisioner->supports($component)) {
                $provisioner->provision($component);

                break;
            }
        }
    }

    /**
     * @param ComponentInterface $component
     *
     * @return bool
     */
    public function supports(ComponentInterface $component)
    {
        foreach ($this->provisioners as $provisioner) {
            if ($provisioner->supports($component)) {
                return true;
            }
        }

        return false;
    }
}
