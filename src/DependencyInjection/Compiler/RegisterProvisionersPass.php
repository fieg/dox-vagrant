<?php

namespace Fieg\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterProvisionersPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $delegatingProvisioner = 'provisioner';

        if (!$container->hasDefinition($delegatingProvisioner)) {
            return;
        }

        $tagName    = 'provisioner';
        $definition = $container->getDefinition($delegatingProvisioner);

        foreach ($container->findTaggedServiceIds($tagName) as $service => $tags) {
            $definition->addMethodCall('addProvisioner', [new Reference($service)]);
        }
    }
}
