<?php

namespace Fieg\Domain\Twig;

interface TwigInterface
{
    /**
     * @param string $template
     * @param array $data
     *
     * @return mixed
     */
    public function render($template, array $data = []);
}
