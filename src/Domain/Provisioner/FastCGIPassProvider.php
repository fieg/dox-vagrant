<?php

namespace Fieg\Domain\Provisioner;

use Fieg\Domain\Docker\Docker;

interface FastCGIPassProvider
{
    /**
     * @param Docker $docker
     *
     * @return string
     */
    public function getFastCGIPass(Docker $docker);
}
