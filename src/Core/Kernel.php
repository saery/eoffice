<?php

/*
 * This file is part of the EOffice project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace EOffice\Core;

use EOffice\Contracts\Support\ModuleInterface;
use EOffice\Core\Exception\CoreException;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @var array<array-key,ModuleInterface>
     */
    private array $modules;

    /**
     * @return array<array-key,ModuleInterface>
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    public function getProjectDir(): string
    {
        if (is_dir($dir = __DIR__.'/../../vendor')) {
            return \dirname($dir);
        }

        return parent::getProjectDir();
    }

    /**
     * @throws CoreException
     */
    protected function initializeBundles(): void
    {
        parent::initializeBundles();
        $this->modules = [];

        foreach ($this->initializeModules() as $module) {
            if ( ! $module instanceof ModuleInterface) {
                $moduleClass = \get_class($module);
                throw CoreException::moduleShouldImplementModuleInterface($moduleClass);
            }
            $this->modules[$module->getName()] = $module;
        }
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $env = $this->getEnvironment();

        $container->import('../../config/{packages}/*.yaml');
        $container->import('../../config/{packages}/'.$env.'/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/services.yaml')) {
            $container->import('../../config/services.yaml');
            $container->import('../../config/{services}_'.$env.'.yaml');
        } else {
            $container->import('../../config/{services}.php');
        }

        foreach ($this->modules as $module) {
            $this->configureModule($container, $module);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $env = $this->getEnvironment();
        $routes->import('../../config/{routes}/'.$env.'/*.yaml');
        $routes->import('../../config/{routes}/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/routes.yaml')) {
            $routes->import('../../config/routes.yaml');
        } else {
            $routes->import('../../config/{routes}.php');
        }

        foreach ($this->getModules() as $module) {
            $baseDir = $module->getBaseDir();
            if (is_file($file = $baseDir.'/Resources/config/routes.yaml')) {
                $routes->import($file);
            }
        }
    }

    /**
     * @throws CoreException
     *
     * @return iterable
     */
    private function initializeModules(): iterable
    {
        $path = $this->getProjectDir().'/config/modules.php';
        if ( ! is_file($path)) {
            throw CoreException::modulesFileNotFound($path);
        }

        /** @var array $modules */
        $modules = require $path;
        foreach ($modules as $class) {
            yield new $class();
        }
    }

    private function configureModule(ContainerConfigurator $container, ModuleInterface $module): void
    {
        $env       = $this->getEnvironment();
        $moduleDir = $module->getBaseDir();
        $name      = $module->getName();

        $container->parameters()->set('eoffice.'.$name.'.module_dir', $moduleDir);
        if (is_file($serviceConfig = $moduleDir.'/Resources/config/services.xml')) {
            $container->import($serviceConfig);
        }
        if (is_file($envConfig = $moduleDir.'/Resources/config/{services}_'.$env.'.yaml')) {
            $container->import($envConfig);
        }
        $resourcesDir = $moduleDir.'/Resources';
        $container->import($resourcesDir.'/{packages}/*.yaml');
        $container->import($resourcesDir.'/{packages}/*.xml');
        $container->import($resourcesDir.'/{packages}/'.$env.'/*.yaml');
        $container->import($resourcesDir.'/{packages}/'.$env.'/*.xml');
    }
}
