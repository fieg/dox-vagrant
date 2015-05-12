<?php

namespace Fieg\Domain\Doxfile;

interface DoxfileLoaderInterface
{
    /**
     * @param string $content
     *
     * @return Doxfile
     */
    public function load($content);
}
