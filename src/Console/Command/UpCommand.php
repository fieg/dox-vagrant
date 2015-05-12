<?php

namespace Fieg\Console\Command;

use Fieg\Domain\Command\Up;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpCommand extends AbstractContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('up');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandBus = $this->container->get('command_bus');

        $command = new Up(file_get_contents('./Doxfile'));

        $commandBus->handle($command);
    }
}
