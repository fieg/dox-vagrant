<?php

namespace Fieg\Domain\Component;

interface GroupInterface 
{
    /**
     * @return ComponentInterface[]
     */
    public function getComponents();

    /**
     * @return integer
     */
    public function getId();
}
