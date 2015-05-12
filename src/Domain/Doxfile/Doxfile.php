<?php

namespace Fieg\Domain\Doxfile;

use Fieg\Domain\Component\GroupInterface;

class Doxfile
{
    /**
     * @var GroupInterface[]
     */
    protected $groups;

    /**
     * @return GroupInterface[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    public function addGroup(GroupInterface $group)
    {
        $this->groups[] = $group;
    }
}
