<?php

namespace Fieg\Domain\Docker;

use Symfony\Component\Process\ProcessBuilder;

class Docker
{
    /**
     * @param string $file
     * @param null|string $tag
     */
    public function build($file, $tag = null)
    {
        $builder = new ProcessBuilder();
        $builder->setPrefix('/usr/bin/docker');
        $builder->add('build');

        if ($tag) {
            $builder->add('-t');
            $builder->add($tag);
        }

        $builder->add('.');

        $builder->setTimeout(null);

        $process = $builder->getProcess();
        $process->setWorkingDirectory(dirname($file));
        $process->mustRun();
        //$output = $process->getOutput();

        // echo $output;
    }

    /**
     * @return string[]
     */
    public function images()
    {
        $builder = new ProcessBuilder();
        $builder->setPrefix('/usr/bin/docker');
        $builder->add('images');
        $builder->add('--no-trunc');

        $process = $builder->getProcess();
        $process->mustRun();
        $output = $process->getOutput();

        $lines = explode("\n", trim($output));
        array_shift($lines);

        $retval = [];

        foreach ($lines as $line) {
            list ($repository, $tag, $id) = array_values(array_filter(array_map('trim', explode("  ", $line))));

            $name = sprintf('%s:%s', $repository, $tag);

            if ($name === '<none>:<none>') {
                $name = $id;
            }

            $retval[] = $name;
        }

        return $retval;
    }

    /**
     * @param string $image
     */
    public function pull($image)
    {
        $builder = new ProcessBuilder();
        $builder->setPrefix('/usr/bin/docker');
        $builder->add('pull');
        $builder->add($image);

        $builder->setTimeout(null);

        $process = $builder->getProcess();
        $process->mustRun();
        // $output = $process->getOutput();
    }

    /**
     * @param string $image
     * @param null $command
     * @param null $name
     * @param array $links
     * @param array $ports
     * @param array $volumes
     *
     * @return string
     */
    public function run($image, $command = null, $name = null, $links = [], $ports = [], $volumes = [])
    {
        $builder = new ProcessBuilder();
        $builder->setPrefix('/usr/bin/docker');
        $builder->add('run');
        $builder->add('-d');

        if ($name) {
            $builder->add('--name');
            $builder->add($name);
        }

        if ($links) {
            foreach ($links as $alias => $link) {
                $builder->add('--link');
                $builder->add($link . ':' . $alias);
            }
        }

        if ($ports) {
            if (is_array($ports)) {
                foreach ($ports as $portContainer => $portHost) {
                    $builder->add('-p');
                    $builder->add($portContainer . ':' . $portHost);
                }
            } else if (true === $ports) {
                $builder->add('-P');
            }
        }

        if ($volumes) {
            foreach ($volumes as $volumeContainer => $volumeHost) {
                $builder->add('-v');
                $builder->add($volumeHost . ':' . $volumeContainer);
            }
        }

        $builder->add($image);

        if ($command) {
            $args = explode(' ', $command);
            foreach ($args as $arg) {
                $builder->add($arg);
            }
        }

        $process = $builder->getProcess();

        $process->mustRun();
        $output = $process->getOutput();
        $error = $process->getErrorOutput();

        return trim($output) . trim($error);
    }

    /**
     * @param bool $runningOnly
     *
     * @return string[]
     */
    public function containers($runningOnly = false)
    {
        $builder = new ProcessBuilder();
        $builder->setPrefix('/usr/bin/docker');
        $builder->add('ps');

        if (!$runningOnly) {
            $builder->add('-a');
        }

        $builder->add('--no-trunc');

        $process = $builder->getProcess();
        $process->mustRun();
        $output = $process->getOutput();

        $lines = explode("\n", trim($output));
        array_shift($lines);

        $retval = [];

        foreach ($lines as $line) {
            list ($id) = array_values(array_filter(array_map('trim', explode("  ", $line))));

            $retval[] = $id;
        }

        return $retval;
    }

    /**
     * @param string $id container id
     */
    public function removeContainer($id)
    {
        $builder = new ProcessBuilder();
        $builder->setPrefix('/usr/bin/docker');
        $builder->add('rm');
        $builder->add('-f');
        $builder->add($id);

        $process = $builder->getProcess();
        $process->mustRun();
        // $output = $process->getOutput();
    }

    /**
     * @param string $name
     */
    public function removeImage($name)
    {
        $builder = new ProcessBuilder();
        $builder->setPrefix('/usr/bin/docker');
        $builder->add('rmi');
        $builder->add('-f');
        $builder->add($name);

        $process = $builder->getProcess();
        $process->mustRun();
        // $output = $process->getOutput();
    }

    /**
     * @param string $id container id
     *
     * @return array
     */
    public function inspect($id)
    {
        $builder = new ProcessBuilder();
        $builder->setPrefix('/usr/bin/docker');
        $builder->add('inspect');
        $builder->add($id);

        $process = $builder->getProcess();
        $process->mustRun();
        $output = $process->getOutput();

        $data = json_decode($output);

        return $data;
    }

    /**
     * @param string $id container id
     * @param string $command
     * @param null $args
     *
     * @return string
     */
    public function exec($id, $command, $args = null)
    {
        $builder = new ProcessBuilder();
        $builder->setPrefix('/usr/bin/docker');
        $builder->add('exec');
        $builder->add($id);
        $builder->add($command);

        if ($args) {
            $builder->add($args);
        }

        $process = $builder->getProcess();
        $process->mustRun();
        $output = $process->getOutput();
        $error = $process->getErrorOutput();

        return trim($output) . trim($error);
    }
}
