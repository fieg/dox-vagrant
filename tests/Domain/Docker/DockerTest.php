<?php

use Fieg\Domain\Docker\Docker;

class DockerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        @mkdir('/tmp/docker/', 0777, true);
    }

    public function testImagesReturnsArrayOfImages()
    {
        $docker = new Docker();
        $actual = $docker->images();

        $this->assertInternalType('array', $actual);
        $this->assertContainsOnly('string', $actual, true);
    }

    public function testContainers()
    {
        $docker = new Docker();
        $actual = $docker->containers();

        $this->assertInternalType('array', $actual);
        $this->assertContainsOnly('string', $actual, true);
    }

    public function testBuild()
    {
        $content = <<<DOCKERFILE
FROM ubuntu:utopic
ENV DEBIAN_FRONTEND noninteractive
RUN date
DOCKERFILE;

        $file = '/tmp/docker/Dockerfile';
        @mkdir(dirname($file, true));

        file_put_contents($file, $content);

        $docker = new Docker();
        $docker->build($file, 'test:build');

        $this->assertContains('test:build', $docker->images());
    }

    public function testRemoveContainer()
    {
        $docker = new Docker();

        $docker->run('test:build', 'date');

        list ($first) = $docker->containers();
        $docker->removeContainer($first);

        $this->assertNotContains($first, $docker->containers());
    }

    public function testRemoveImage()
    {
        $docker = new Docker();

        $docker->removeImage('test:build');

        $this->assertNotContains('test:build', $docker->images());
    }

    public function testPull()
    {
        $docker = new Docker();
        $docker->pull('ubuntu');

        $this->assertContains('ubuntu:utopic', $docker->images());
    }

    public function testRun()
    {
        $docker = new Docker();

        $docker->pull('ubuntu');

        $actual = $docker->run('ubuntu:utopic', 'pwd');

        // clean up
        $docker->removeContainer(reset($docker->containers()));

        $this->assertInternalType('string', $actual);
    }
}
