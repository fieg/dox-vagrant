<?php

namespace Fieg\Command;

use Fieg\Domain\Command\CommandBusInterface;
use Fieg\Domain\Command\CommandInterface;
use Fieg\Domain\Command\Handler\HandlerInterface;

class CommandBus implements CommandBusInterface
{
    /**
     * @var HandlerInterface[]
     */
    protected $handlers = [];

    public function handle(CommandInterface $command)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($command)) {
                $handler->handle($command);
            }
        }
    }

    public function addHandler(HandlerInterface $handler)
    {
        $this->handlers[] = $handler;
    }
}
