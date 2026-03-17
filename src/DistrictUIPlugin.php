<?php
declare(strict_types=1);

namespace DistrictUI;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\ContainerInterface;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\RouteBuilder;
use DistrictUI\Command\DistrictUiCommand;
use DistrictUI\Command\InstallCommand;

class DistrictUIPlugin extends BasePlugin
{
    /**
     * @param \Cake\Core\PluginApplicationInterface $app The host application
     * @return void
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        $configFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bootstrap.php';
        if (is_file($configFile)) {
            require $configFile;
        }
    }

    /**
     * @param \Cake\Routing\RouteBuilder $routes The route builder to update.
     * @return void
     */
    public function routes(RouteBuilder $routes): void
    {
        parent::routes($routes);
    }

    /**
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to update.
     * @return \Cake\Http\MiddlewareQueue
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue;
    }

    /**
     * @param \Cake\Console\CommandCollection $commands The command collection to update.
     * @return \Cake\Console\CommandCollection
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        $commands = parent::console($commands);
        $commands->addMany([
            DistrictUiCommand::defaultName() => DistrictUiCommand::class,
            InstallCommand::defaultName() => InstallCommand::class,
        ]);

        return $commands;
    }

    /**
     * @param \Cake\Core\ContainerInterface $container The Container to update.
     * @return void
     */
    public function services(ContainerInterface $container): void
    {
    }
}
