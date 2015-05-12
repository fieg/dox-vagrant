<?php

namespace Fieg\Console;

use Fieg\Console\Command\UpCommand;
use Fieg\DependencyInjection\Compiler\RegisterCommandHandlersPass;
use Fieg\DependencyInjection\Compiler\RegisterProvisionersPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Application extends BaseApplication
{
    private $container;

    public function __construct()
    {
        $this->container = $this->createContainer();

        parent::__construct();
    }

    public function add(Command $command)
    {
        if ($command instanceof ContainerAwareInterface) {
            $command->setContainer($this->container);
        }

        return parent::add($command);
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        return array_merge($commands, [
            new UpCommand(),
        ]);
    }

    protected function createContainer()
    {
        $container = new ContainerBuilder();
        $container->addCompilerPass(new RegisterProvisionersPass());
        $container->addCompilerPass(new RegisterCommandHandlersPass());

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->compile();

        return $container;
    }
}
