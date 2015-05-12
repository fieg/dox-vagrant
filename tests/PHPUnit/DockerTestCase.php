<?php

namespace PHPUnit;

class DockerTestCase extends \PHPUnit_Framework_TestCase
{
    protected function purgeContainers()
    {
        $docker = new \Fieg\Domain\Docker\Docker();

        foreach ($docker->containers() as $id) {
            $docker->removeContainer($id);
        }
    }

    protected function purgeImages($exclude = [])
    {
        $docker = new \Fieg\Domain\Docker\Docker();

        foreach ($docker->images() as $name) {
            if (!in_array($name, $exclude)) {
                $docker->removeImage($name);
            }
        }
    }

    protected function purgeImage($name)
    {
        $docker = new \Fieg\Domain\Docker\Docker();

        if (in_array($name, $docker->images())) {
            $docker->removeImage($name);
        }
    }

    protected function assertImageExists($image)
    {
        $docker = new \Fieg\Domain\Docker\Docker();

        $images = $docker->images();

        $this->assertArraySubset([$image], $images);
    }

    protected function assertImageFileEquals($image, $file, $expected)
    {
        $docker = new \Fieg\Domain\Docker\Docker();

        $actual = $docker->run($image, sprintf('cat %s', $file));

        $this->assertEquals($expected, $actual);
    }

    protected function assertImageFileContains($image, $file, $expected)
    {
        $docker = new \Fieg\Domain\Docker\Docker();

        $actual = $docker->run($image, sprintf('cat %s', $file));

        $this->assertContains($expected, $actual);
    }
}
