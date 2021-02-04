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
namespace HyperfTest\ModelCache;

use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\DbConnection\Collector\TableCollector;
use Hyperf\ModelCache;
use Hyperf\ModelCache\Handler\HandlerInterface;
use Hyperf\Utils\ApplicationContext;
use HyperfTest\ModelCache\Stub\ManagerStub;
use HyperfTest\ModelCache\Stub\ModelStub;
use HyperfTest\ModelCache\Stub\NonHandler;
use HyperfTest\ModelCache\Stub\StdoutLogger;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 * @coversNothing
 */
class ManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testFormatModel()
    {
        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('get')->once()->with(StdoutLoggerInterface::class)->andReturn(new StdoutLogger());
        $container->shouldReceive('get')->once()->with(ConfigInterface::class)->andReturn(new Config($this->getConfig()));
        $container->shouldReceive('make')->with(ContainerInterface::class)->andReturn($container);
        $container->shouldReceive('get')->with(EventDispatcherInterface::class)->andReturn(null);
        $container->shouldReceive('get')->with(TableCollector::class)->andReturn(new TableCollector());

        ApplicationContext::setContainer($container);

        $manager = new ManagerStub($container);

        $model = new ModelStub();
        $json = ['id' => 1, 'name' => 'Hyperf'];
        $model->fill(['id' => 1, 'json_data' => $json, 'str' => null, 'float_num' => 0.1]);

        $this->assertSame(['id' => 1, 'json_data' => json_encode($json), 'str' => null, 'float_num' => 0.1], $model->getAttributes());
        $this->assertSame(['id' => 1, 'json_data' => ['id' => 1, 'name' => 'Hyperf'], 'str' => null, 'float_num' => 0.1], $model->toArray());
        $res = $manager->formatModel($model);

        $this->assertSame(['id' => 1, 'json_data' => json_encode($json), 'str' => null, 'float_num' => 0.1], $res);
    }

    public function testGetCacheTTL()
    {
        $container = Mockery::mock(ContainerInterface::class);
        $container->shouldReceive('get')->once()->with(StdoutLoggerInterface::class)->andReturn(new StdoutLogger());
        $container->shouldReceive('get')->once()->with(ConfigInterface::class)->andReturn(new Config($this->getConfig()));
        $container->shouldReceive('make')->with(ContainerInterface::class)->andReturn($container);
        $container->shouldReceive('get')->with(EventDispatcherInterface::class)->andReturn(null);
        $container->shouldReceive('get')->with(TableCollector::class)->andReturn(new TableCollector());

        ApplicationContext::setContainer($container);
        $handler = Mockery::mock(HandlerInterface::class);
        $handler->shouldReceive('getConfig')->andReturnUsing(function () {
            return new ModelCache\Config([
                'ttl' => 1000,
            ], 'default');
        });
        $manager = new ManagerStub($container);

        $model = new ModelStub();
        $this->assertSame(1000, $manager->getCacheTTL($model, $handler));
        $model = new class() extends ModelStub implements ModelCache\CacheableInterface {
            use ModelCache\Cacheable;

            public function getCacheTTL(): ?int
            {
                return 100;
            }
        };
        $this->assertSame(100, $manager->getCacheTTL($model, $handler));
    }

    public function testInvalidCacheManager()
    {
        parallel([static function () {
            $manager = ModelCache\InvalidCacheManager::instance();
            $model = Mockery::mock(ModelCache\CacheableInterface::class);
            $model->shouldReceive('deleteCache')->once()->andReturn(true);
            $manager->push($model);
            $manager->delete();
            $manager->delete();
        }]);

        $this->assertInstanceOf(ModelCache\InvalidCacheManager::class, ModelCache\InvalidCacheManager::instance());
    }

    protected function getConfig(): array
    {
        return [
            'databases' => [
                'default' => [
                    'cache' => [
                        'handler' => NonHandler::class,
                        'cache_key' => 'mc:%s:m:%s:%s:%s',
                        'prefix' => 'default',
                        'pool' => 'default',
                        'ttl' => 3600 * 24,
                        'empty_model_ttl' => 3600,
                        'load_script' => true,
                    ],
                ],
            ],
        ];
    }
}
