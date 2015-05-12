<?php

namespace Fieg\Domain\Component;

interface ComponentInterface 
{
    /**
     * @return string
     */
    public static function getNamespace();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param GroupInterface $group
     */
    public function setGroup(GroupInterface $group);

    /**
     * @param ComponentInterface[] $components
     *
     * @return ComponentInterface[]
     */
    public function requires(array $components);
}
