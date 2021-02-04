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
namespace HyperfTest\Utils;

use Hyperf\Utils\Arr;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ArrTest extends TestCase
{
    public function testArrGet()
    {
        $data = ['id' => 1, 'name' => 'Hyperf'];
        $this->assertSame(1, Arr::get($data, 'id'));
        $this->assertSame('Hyperf', Arr::get($data, 'name'));
        $this->assertSame($data, Arr::get($data));
        $this->assertSame(null, Arr::get($data, 'gendar'));
        $this->assertSame(1, Arr::get($data, 'gendar', 1));

        $data = [1, 2, 3, 4];
        $this->assertSame(1, Arr::get($data, 0));
        $this->assertSame(5, Arr::get($data, 4, 5));
        $this->assertSame(null, Arr::get($data, 5));

        $object = new \stdClass();
        $object->id = 1;
        $this->assertSame(null, Arr::get($object, 'id'));
    }

    public function testArrSet()
    {
        $data = ['id' => 1, 'name' => 'Hyperf'];
        Arr::set($data, 'id', 2);
        $this->assertSame(['id' => 2, 'name' => 'Hyperf'], $data);
        Arr::set($data, 'gendar', 2);
        $this->assertSame(['id' => 2, 'name' => 'Hyperf', 'gendar' => 2], $data);
        Arr::set($data, 'book.0', 'Hello Hyperf');
        $this->assertSame(['id' => 2, 'name' => 'Hyperf', 'gendar' => 2, 'book' => ['Hello Hyperf']], $data);
        Arr::set($data, 'rel.id', 2);
        $this->assertSame(['id' => 2, 'name' => 'Hyperf', 'gendar' => 2, 'book' => ['Hello Hyperf'], 'rel' => ['id' => 2]], $data);
        Arr::set($data, null, [1, 2, 3]);
        $this->assertSame([1, 2, 3], $data);

        $data = [1, 2, 3, 4];
        Arr::set($data, 0, 2);
        $this->assertSame([2, 2, 3, 4], $data);
        Arr::set($data, 4, 2);
        $this->assertSame([2, 2, 3, 4, 2], $data);
    }

    public function testArrMerge()
    {
        $this->assertSame([1, 2, 3, 4], Arr::merge([1, 2, 3], [2, 3, 4]));
        $this->assertSame([1, 2, 3, 2, 3, 4], Arr::merge([1, 2, 3], [2, 3, 4], false));
        $this->assertSame([1, 2, [1, 2], 3, 4], Arr::merge([1, 2, [1, 2]], [[1, 2], 3, 4]));
        $this->assertSame([1, 2, [1, 2], [1, 2], 3, 4], Arr::merge([1, 2, [1, 2]], [[1, 2], 3, 4], false));
        $this->assertSame([1, 2, 3, '2', 4], Arr::merge([1, 2, 3], ['2', 3, 4]));

        $this->assertSame(['id' => 1, 'name' => 'Hyperf'], Arr::merge(['id' => 1], ['name' => 'Hyperf']));
        $this->assertSame(['id' => 1, 'name' => 'Hyperf', 'gender' => 1], Arr::merge(['id' => 1, 'name' => 'Swoole'], ['name' => 'Hyperf', 'gender' => 1]));
        $this->assertSame(['id' => 1, 'ids' => [1, 2, 3], 'name' => 'Hyperf'], Arr::merge(['id' => 1, 'ids' => [1, 2]], ['name' => 'Hyperf', 'ids' => [1, 2, 3]]));
        $this->assertSame(['id' => 1, 'ids' => [1, 2, 1, 2, 3], 'name' => 'Hyperf'], Arr::merge(['id' => 1, 'ids' => [1, 2]], ['name' => 'Hyperf', 'ids' => [1, 2, 3]], false));

        $this->assertSame(['id' => 1, 'name' => ['Hyperf']], Arr::merge(['id' => 2], ['id' => 1, 'name' => ['Hyperf']]));
        $this->assertSame(['id' => 1, 'name' => 'Hyperf'], Arr::merge([], ['id' => 1, 'name' => 'Hyperf']));
        $this->assertSame([1, 2, 3], Arr::merge([], [1, 2, 3]));
        $this->assertSame([1, 2, 3], Arr::merge([], [1, 2, 2, 3]));
        $this->assertSame([1, 2, [1, 2, 3]], Arr::merge([], [1, 2, 2, [1, 2, 3], [1, 2, 3]]));
        $this->assertSame([1, 2, [1, 2, 3], [1, 2, 3, 4]], Arr::merge([], [1, 2, 2, [1, 2, 3], [1, 2, 3, 4]]));
        $this->assertSame([1, 2, 3], Arr::merge([1, 2], ['id' => 3]));
        $this->assertSame([1, 2, 'id' => 3], Arr::merge([], [1, 2, 'id' => 3]));

        $array1 = [
            'logger' => [
                'default' => [
                    'handler' => [
                        'class' => \Monolog\Handler\StreamHandler::class,
                        'constructor' => [
                            'stream' => BASE_PATH . '/runtime/logs/hyperf.log',
                            'level' => \Monolog\Logger::DEBUG,
                        ],
                    ],
                ],
            ],
            'scan' => [
                'paths' => [
                    BASE_PATH . '/app',
                ],
                'ignore_annotations' => [
                    'mixin',
                ],
                'class_map' => [
                    \Hyperf\Utils\Coroutine::class => BASE_PATH . '/app/Kernel/ClassMap/Coroutine.php',
                ],
            ],
        ];

        $array2 = [
            'logger' => [
                'default' => [
                    'handler' => [
                        'class' => \Monolog\Handler\StreamHandler::class,
                        'constructor' => [
                            'stream' => BASE_PATH . '/runtime/logs/hyperf.log',
                            'level' => \Monolog\Logger::INFO,
                        ],
                    ],
                ],
            ],
            'scan' => [
                'ignore_annotations' => [
                    'mixin',
                    'author',
                ],
                'class_map' => [
                    \Hyperf\Utils\Coroutine::class => BASE_PATH . '/app/Kernel/ClassMap/Coroutine.php',
                    \Hyperf\Di\Resolver\ResolverDispatcher::class => BASE_PATH . '/vendor/hyperf/di/class_map/Resolver/ResolverDispatcher.php',
                ],
            ],
        ];

        $result = Arr::merge($array1, $array2);
        $array1['logger']['default']['handler']['constructor']['level'] = \Monolog\Logger::INFO;
        $array1['scan']['class_map'][\Hyperf\Di\Resolver\ResolverDispatcher::class] = BASE_PATH . '/vendor/hyperf/di/class_map/Resolver/ResolverDispatcher.php';
        $array1['scan']['ignore_annotations'][] = 'author';

        $this->assertSame($array1, $result);

        $result = Arr::merge($result, $array2);
        $this->assertSame($array1, $result);
    }
}
