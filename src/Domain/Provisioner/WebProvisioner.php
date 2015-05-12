<?php

namespace Fieg\Domain\Provisioner;

use Fieg\Domain\Component\ComponentInterface;
use Fieg\Domain\Component\WebComponent;
use Fieg\Domain\Docker\Docker;
use Fieg\Domain\Twig\TwigInterface;

class WebProvisioner implements ProvisionerInterface
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
     * @param ComponentInterface|WebComponent $component
     */
    public function provision(ComponentInterface $component)
    {
        $docker = new Docker();

        $buildDir = $this->buildDir;
        @mkdir($buildDir, 0777, true);

        $file = $buildDir . '/Dockerfile';

        $siteConfigFile = tempnam($buildDir, 'site_conf');

        $options = [];

        $components = $component->getGroup()->getComponents();
        foreach ($components as $_component) {
            if ($_component instanceof FrontControllerProvider) {
                $options['nginx']['controller'] = $_component->getFrontController();

                break;
            }
        }
        foreach ($components as $_component) {
            if ($_component instanceof FastCGIPassProvider) {
                $options['nginx']['fastcgi_pass'] = $_component->getFastCGIPass($docker);

                break;
            }
        }

        $this->createNginxSiteConfig($options, $siteConfigFile);
        $this->createDockerfile($file, basename($siteConfigFile));
        $this->createSupervisorConfig($buildDir . '/supervisor.conf');

        $docker->build($file, sprintf('%s:latest', $component->getName()));

        $docker->run(sprintf('%s:latest', $component->getName()), null, $component->getName(), [], [80 => 80], ['/mnt' => realpath(__DIR__ . '/../../../')]);

        $this->waitUntilReady($component->getName(), 80);
    }

    /**
     * @param string $file
     * @param string $siteConfigFile
     */
    protected function createDockerfile($file, $siteConfigFile)
    {
        $dockerfileTemplateFile = __DIR__ . '/../Resources/docker/web/Dockerfile.twig';
        $content = $this->twig->render($dockerfileTemplateFile, [
            'site_config' => $siteConfigFile,
        ]);

        file_put_contents($file, $content);
    }

    /**
     * @param array $options
     * @param string $file
     */
    protected function createNginxSiteConfig(array $options = null, $file)
    {
        $nginxSiteConfTemplateFile = __DIR__ . '/../Resources/docker/web/site.conf.twig';

        $nginxSiteConfigContent = $this->twig->render(
            $nginxSiteConfTemplateFile,
            $options
        );

        file_put_contents($file, $nginxSiteConfigContent);
    }

    /**
     * @param $file
     */
    protected function createSupervisorConfig($file)
    {
        $source = __DIR__ . '/../Resources/docker/web/supervisor.conf';

        file_put_contents($file, file_get_contents($source));
    }

    /**
     * @param ComponentInterface $component
     *
     * @return bool
     */
    public function supports(ComponentInterface $component)
    {
        return ($component instanceof WebComponent);
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
