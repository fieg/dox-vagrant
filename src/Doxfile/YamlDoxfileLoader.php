<?php

namespace Fieg\Doxfile;

use Fieg\Domain\Component\AppComponent;
use Fieg\Domain\Component\Group;
use Fieg\Domain\Component\WebComponent;
use Fieg\Domain\Doxfile\Doxfile;
use Fieg\Domain\Doxfile\DoxfileLoaderInterface;
use Symfony\Component\Yaml\Yaml;

class YamlDoxfileLoader implements DoxfileLoaderInterface
{
    /**
     * @var Yaml
     */
    protected $yaml;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param Yaml $yaml
     */
    public function __construct(Yaml $yaml)
    {
        $this->yaml = $yaml;
    }

    /**
     * @param string $content
     *
     * @return Doxfile
     */
    public function load($content)
    {
        $doxfile = new Doxfile();

        $config = $this->yaml->parse($content);

        $sections = array_keys($config);

        $groups = [];

        foreach ($sections as $section) {
            $configuration = null;

            if (preg_match('/^([a-z]+)(\d+)$/i', $section, $matches)) {
                list (, $section, $group) = $matches;

                $groups[(int) $group][] = $section;
            }
        }

        foreach ($groups as $id => $components) {
            $group = new Group($id);

            foreach ($components as $componentNamespace) {
                if ($componentNamespace === WebComponent::getNamespace()) {
                    $component = new WebComponent();
                } else if ($componentNamespace === AppComponent::getNamespace()) {
                    $component = new AppComponent();
                } else {
                    // unsupported
                    continue;
                }

                $group->addComponent($component);
            }

            $doxfile->addGroup($group);
        }

        return $doxfile;
    }
}
