<?php

namespace Fieg\Domain\Component;

class DependencySolver
{
    /**
     * @param ComponentInterface[] $components
     *
     * @return ComponentInterface[]
     */
    public function solve($components)
    {
        $frontier = $components;
        $solved = [];

        /** @var ComponentInterface $component */
        while ($component = array_shift($frontier)) {
            $requirements = $component->requires($components);

            if (true === ($unmet = $this->requirementsMet($solved, $component, $requirements))) {
                if (!$this->contains([$component], $solved)) {
                    $solved[] = $component;
                }
            } else {
                $items = array_merge($unmet, [$component]);

                if (!$this->contains($items, array_slice($frontier, 1))) {
                    foreach ($items as $req) {
                        $frontier[] = $req;
                    }
                } else {
                    throw new \LogicException(sprintf('Circular requirement detected for %s!', $component->getName()));
                }
            }
        }

        return $solved;
    }

    /**
     * @param array $solved
     * @param ComponentInterface $component
     * @param array$requirements
     *
     * @return bool|array
     */
    protected function requirementsMet(array $solved, $component, array $requirements)
    {
        $unmet = [];

        foreach ($requirements as $requirement) {
            if ($requirement === $component) {
                throw new \LogicException('Requires it self');
            }

            if (!in_array($requirement, $solved, true)) {
                $unmet[] = $requirement;
            }
        }

        return $unmet ?: true;
    }

    /**
     * @param array $items
     * @param array $frontier
     *
     * @return bool
     */
    protected function contains(array $items, array $frontier)
    {
        foreach ($items as $item) {
            if (!in_array($item, $frontier, true)) {
                return false;
            }
        }

        return true;
    }
}
