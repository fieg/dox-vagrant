<?php

namespace Fieg\Domain\Provisioner;

use Fieg\Domain\Component\AppComponent;
use Fieg\Domain\Component\ComponentInterface;
use Fieg\Domain\Docker\Docker;
use Fieg\Domain\Twig\TwigInterface;

class AppProvisioner implements ProvisionerInterface
{
    /**
     * @var TwigInterface
     */
    protected $twig;

    /**
     * @var string
     */
    protected $buildDir;

    /**
     * @param string $buildDir
     * @param TwigInterface $twig
     */
    public function __construct($buildDir, TwigInterface $twig)
    {
        $this->buildDir = $buildDir;
        $this->twig = $twig;
    }

    /**
     * @param ComponentInterface $component
     */
    public function provision(ComponentInterface $component)
    {
        $buildDir = $this->buildDir;
        @mkdir($buildDir, 0777, true);

        $file = $buildDir . '/Dockerfile';

        $siteConfigFile = tempnam($buildDir, 'site_conf');

        $this->createDockerfile($file, basename($siteConfigFile));
        $this->createSupervisorConfig($buildDir . '/supervisor.conf');
        $this->createWwwConfig($buildDir . '/www.conf');

        $docker = new Docker();
        $docker->build($file, sprintf('%s:latest', $component->getName()));

        $docker->run(sprintf('%s:latest', $component->getName()), null, $component->getName(), [], [], ['/mnt' => realpath(__DIR__ . '/../../../')]);

        $this->waitUntilReady($component->getName(), 9000);
    }

    /**
     * @param ComponentInterface $component
     *
     * @return bool
     */
    public function supports(ComponentInterface $component)
    {
        return ($component instanceof AppComponent);
    }

    /**
     * @param string $file
     */
    protected function createDockerfile($file)
    {
        $dockerfileTemplateFile = __DIR__ . '/../Resources/docker/app-php/Dockerfile.twig';
        $content = $this->twig->render($dockerfileTemplateFile);

        file_put_contents($file, $content);
    }

    /**
     * @param string $file
     */
    protected function createSupervisorConfig($file)
    {
        $source = __DIR__ . '/../Resources/docker/app-php/supervisor.conf';

        file_put_contents($file, file_get_contents($source));
    }

    /**
     * @param string $file
     */
    private function createWwwConfig($file)
    {
        $source = __DIR__ . '/../Resources/docker/app-php/www.conf';

        file_put_contents($file, file_get_contents($source));
    }

    /**
     * @param string $id container id
     * @param int $port
     * @param int $timeout
     */
    private function waitUntilReady($id, $port, $timeout = 3)
    {
        $docker = new Docker();

        $start = time();

        $info = $docker->inspect($id);
        $ip = $info[0]->NetworkSettings->IPAddress;

        while (!($f = @fsockopen($ip, $port, $errno, $errstr, 0))) {
            if (time() - $start > $timeout) {
                return;
            }
            sleep(1);
        }

        fclose($f);
    }
}
