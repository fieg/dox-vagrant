<?php

namespace Fieg\Domain\Provisioner;

interface FrontControllerProvider 
{
    /**
     * @return string
     */
    public function getFrontController();
}
