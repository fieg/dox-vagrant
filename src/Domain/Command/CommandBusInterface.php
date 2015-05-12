<?php

namespace Fieg\Domain\Command;

interface CommandBusInterface
{
    /**
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command);
}
