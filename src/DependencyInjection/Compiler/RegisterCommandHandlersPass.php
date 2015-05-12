<?php

namespace Fieg\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterCommandHandlersPass implements CompilerPassInterface
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
        $commandBus = 'command_bus';

        if (!$container->hasDefinition($commandBus)) {
            return;
        }

        $tagName    = 'command_handler';
        $definition = $container->getDefinition($commandBus);

        foreach ($container->findTaggedServiceIds($tagName) as $service => $tags) {
            $definition->addMethodCall('addHandler', [new Reference($service)]);
        }
    }
}
