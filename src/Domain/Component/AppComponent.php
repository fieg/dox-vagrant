<?php

namespace Fieg\Domain\Component;

use Fieg\Domain\Docker\Docker;
use Fieg\Domain\Provisioner\FastCGIPassProvider;
use Fieg\Domain\Provisioner\FrontControllerProvider;

class AppComponent extends AbstractComponent implements ComponentInterface, FrontControllerProvider, FastCGIPassProvider
{
    /**
     * @return string
     */
    public static function getNamespace()
    {
        return 'app';
    }

    /**
     * @return string
     */
    public function getFrontController()
    {
        return 'app.php';
    }

    /**
     * @param Docker $docker
     *
     * @return string
     */
    public function getFastCGIPass(Docker $docker)
    {
        $info = $docker->inspect($this->getName());
        $ip = $info[0]->NetworkSettings->IPAddress;

        return sprintf('%s:9000', $ip);
    }
}
