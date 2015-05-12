<?php

namespace Fieg\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class AbstractContainerAwareCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;
}
