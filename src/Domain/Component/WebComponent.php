<?php

namespace Fieg\Domain\Component;

use Fieg\Domain\Provisioner\FastCGIPassProvider;
use Fieg\Domain\Provisioner\FrontControllerProvider;

class WebComponent extends AbstractComponent implements ComponentInterface
{
    /**
     * @return string
     */
    public static function getNamespace()
    {
        return 'web';
    }

    /**
     * @param ComponentInterface[] $components
     *
     * @return ComponentInterface[]
     */
    public function requires(array $components)
    {
        $requirements = [];

        foreach ($components as $component) {
            switch (true) {
                case $component instanceof FrontControllerProvider:
                case $component instanceof FastCGIPassProvider:
                    $requirements[] = $component;
                    break;
            }
        }

        return $requirements;
    }
}
