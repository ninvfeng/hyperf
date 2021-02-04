<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\Database\Stubs;

use Hyperf\Database\Commands\ModelOption;
use Hyperf\Database\ConnectionResolver;
use Hyperf\Database\ConnectionResolverInterface;
use Hyperf\Database\Connectors\ConnectionFactory;
use Hyperf\Database\Connectors\MySqlConnector;
use Hyperf\Di\Container;
use Hyperf\Utils\ApplicationContext;
use Mockery;

class ContainerStub
{
    public static function getContainer($callback = null)
    {
        $container = Mockery::mock(Container::class);
        ApplicationContext::setContainer($container);

        $container->shouldReceive('has')->andReturn(true);
        $container->shouldReceive('get')->with('db.connector.mysql')->andReturn(new MySqlConnector());
        $connector = new ConnectionFactory($container);

        $dbConfig = [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'database' => 'hyperf',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ];

        $connection = $connector->make($dbConfig);
        if (is_callable($callback)) {
            $callback($connection);
        }

        $resolver = new ConnectionResolver(['default' => $connection]);

        $container->shouldReceive('get')->with(ConnectionResolverInterface::class)->andReturn($resolver);

        return $container;
    }

    public static function getModelOption()
    {
        $option = new ModelOption();
        $option->setWithComments(false)
            ->setRefreshFillable(true)
            ->setForceCasts(true)
            ->setInheritance('Model')
            ->setPath(__DIR__ . '/../Stubs/Model')
            ->setPool('default')
            ->setPrefix('')
            ->setWithIde(false);
        return $option;
    }
}
