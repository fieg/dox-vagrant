<?php

namespace Fieg\Domain\Command;

class Up implements CommandInterface
{
    /**
     * @var string contents of Doxfile
     */
    private $doxfile;

    /**
     * @param string $doxfile
     */
    public function __construct($doxfile)
    {
        $this->doxfile = $doxfile;
    }

    /**
     * @return mixed
     */
    public function getDoxfile()
    {
        return $this->doxfile;
    }

    /**
     * @param mixed $doxfile
     *
     * @return $this
     */
    public function setDoxfile($doxfile)
    {
        $this->doxfile = $doxfile;

        return $this;
    }
}
