<?php

namespace Fieg\Twig;

use Fieg\Domain\Twig\TwigInterface;

class Twig implements TwigInterface
{
    protected $twigEnvironment;

    public function __construct(\Twig_Environment $twigEnvironment)
    {
        $this->twigEnvironment = $twigEnvironment;
    }

    public function render($template, array $data = [])
    {
        return $this->twigEnvironment->render($template, $data);
    }
}
