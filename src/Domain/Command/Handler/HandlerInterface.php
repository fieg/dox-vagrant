<?php

namespace Fieg\Domain\Command\Handler;

use Fieg\Domain\Command\CommandInterface;

interface HandlerInterface
{
    /**
     * @param CommandInterface $command
     */
    public function handle(CommandInterface $command);

    /**
     * @param CommandInterface $command
     *
     * @return bool
     */
    public function supports(CommandInterface $command);
}
