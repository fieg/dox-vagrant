<?php

namespace Fieg\Domain\Component;

class Group implements GroupInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var ComponentInterface[]
     */
    protected $components;

    /**
     * @param integer $id
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ComponentInterface[]
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @param ComponentInterface $component
     */
    public function addComponent(ComponentInterface $component)
    {
        $component->setGroup($this);

        $this->components[] = $component;
    }
}
