<?php

namespace Fieg\Domain\Component;

abstract class AbstractComponent implements ComponentInterface
{
    /**
     * @var GroupInterface
     */
    protected $group;

    /**
     * @param GroupInterface $group
     */
    public function setGroup(GroupInterface $group)
    {
        $this->group = $group;
    }

    /**
     * @return GroupInterface
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return sprintf('%s%d', static::getNamespace(), $this->group->getId());
    }

    /**
     * @param ComponentInterface[] $components
     *
     * @return ComponentInterface[]
     */
    public function requires(array $components)
    {
        return [];
    }
}
