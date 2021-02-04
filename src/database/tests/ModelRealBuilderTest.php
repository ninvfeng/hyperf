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
namespace HyperfTest\Database;

use Carbon\Carbon;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\Database\ConnectionInterface;
use Hyperf\Database\ConnectionResolverInterface;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Database\Model\Events\Saved;
use Hyperf\Database\Schema\Column;
use Hyperf\Database\Schema\MySqlBuilder;
use Hyperf\Paginator\LengthAwarePaginator;
use HyperfTest\Database\Stubs\ContainerStub;
use HyperfTest\Database\Stubs\Model\User;
use HyperfTest\Database\Stubs\Model\UserExt;
use HyperfTest\Database\Stubs\Model\UserExtCamel;
use HyperfTest\Database\Stubs\Model\UserRole;
use HyperfTest\Database\Stubs\Model\UserRoleMorphPivot;
use HyperfTest\Database\Stubs\Model\UserRolePivot;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Swoole\Coroutine\Channel;

/**
 * @internal
 * @coversNothing
 */
class ModelRealBuilderTest extends TestCase
{
    /**
     * @var array
     */
    protected $channel;

    protected function setUp(): void
    {
        $this->channel = new Channel(999);
    }

    protected function tearDown(): void
    {
        $container = $this->getContainer();
        /** @var ConnectionInterface $conn */
        $conn = $container->get(ConnectionResolverInterface::class)->connection();
        $conn->statement('DROP TABLE IF EXISTS `test`;');
        Mockery::close();
    }

    public function testPivot()
    {
        $this->getContainer();

        $user = User::query()->find(1);
        $role = $user->roles->first();
        $this->assertSame(1, $role->id);
        $this->assertSame('author', $role->name);

        $this->assertInstanceOf(UserRolePivot::class, $role->pivot);
        $this->assertSame(1, $role->pivot->user_id);
        $this->assertSame(1, $role->pivot->role_id);

        $role->pivot->updated_at = $now = Carbon::now()->toDateTimeString();
        $role->pivot->save();

        $pivot = UserRole::query()->find(1);
        $this->assertSame($now, $pivot->updated_at->toDateTimeString());

        while ($event = $this->channel->pop(0.001)) {
            if ($event instanceof Saved) {
                $this->assertSame($event->getModel(), $role->pivot);
                $hit = true;
            }
        }

        $this->assertTrue($hit);
    }

    public function testForPageBeforeId()
    {
        $this->getContainer();

        User::query()->forPageBeforeId(2)->get();
        User::query()->forPageBeforeId(2, null)->get();
        User::query()->forPageBeforeId(2, 1)->get();

        $sqls = [
            ['select * from `user` where `id` < ? order by `id` desc limit 2', [0]],
            ['select * from `user` order by `id` desc limit 2', []],
            ['select * from `user` where `id` < ? order by `id` desc limit 2', [1]],
        ];
        while ($event = $this->channel->pop(0.001)) {
            if ($event instanceof QueryExecuted) {
                $this->assertSame([$event->sql, $event->bindings], array_shift($sqls));
            }
        }
    }

    public function testForPageAfterId()
    {
        $this->getContainer();

        User::query()->forPageAfterId(2)->get();
        User::query()->forPageAfterId(2, null)->get();
        User::query()->forPageAfterId(2, 1)->get();

        $sqls = [
            ['select * from `user` where `id` > ? order by `id` asc limit 2', [0]],
            ['select * from `user` order by `id` asc limit 2', []],
            ['select * from `user` where `id` > ? order by `id` asc limit 2', [1]],
        ];
        while ($event = $this->channel->pop(0.001)) {
            if ($event instanceof QueryExecuted) {
                $this->assertSame([$event->sql, $event->bindings], array_shift($sqls));
            }
        }
    }

    public function testIncrement()
    {
        $this->getContainer();

        /** @var UserExt $ext */
        $ext = UserExt::query()->find(1);
        $ext->timestamps = false;

        $this->assertFalse($ext->isDirty());

        $ext->increment('count', 1);
        $this->assertFalse($ext->isDirty());
        $this->assertArrayHasKey('count', $ext->getChanges());
        $this->assertSame(1, count($ext->getChanges()));

        $ext->increment('count', 1, [
            'str' => uniqid(),
        ]);
        $this->assertTrue($ext->save());
        $this->assertFalse($ext->isDirty());
        $this->assertArrayHasKey('str', $ext->getChanges());
        $this->assertArrayHasKey('count', $ext->getChanges());
        $this->assertSame(2, count($ext->getChanges()));

        // Don't effect.
        $ext->str = uniqid();
        $this->assertTrue($ext->isDirty('str'));

        $ext->increment('count', 1, [
            'float_num' => (string) ($ext->float_num + 1),
        ]);
        $this->assertTrue($ext->isDirty('str'));
        $this->assertArrayHasKey('count', $ext->getChanges());
        $this->assertArrayHasKey('float_num', $ext->getChanges());

        $this->assertSame(2, count($ext->getChanges()));
        $this->assertTrue($ext->save());
        $this->assertArrayHasKey('str', $ext->getChanges());
        $this->assertSame(1, count($ext->getChanges()));

        $ext->float_num = (string) ($ext->float_num + 1);
        $this->assertTrue($ext->save());
        $this->assertArrayHasKey('float_num', $ext->getChanges());
        $this->assertSame(1, count($ext->getChanges()));

        $sqls = [
            'select * from `user_ext` where `user_ext`.`id` = ? limit 1',
            'update `user_ext` set `count` = `count` + 1 where `id` = ?',
            'update `user_ext` set `count` = `count` + 1, `str` = ? where `id` = ?',
            'update `user_ext` set `count` = `count` + 1, `float_num` = ? where `id` = ?',
            'update `user_ext` set `str` = ? where `id` = ?',
            'update `user_ext` set `float_num` = ? where `id` = ?',
        ];
        while ($event = $this->channel->pop(0.001)) {
            if ($event instanceof QueryExecuted) {
                $this->assertSame($event->sql, array_shift($sqls));
            }
        }
    }

    public function testCamelCaseGetModel()
    {
        $this->getContainer();

        /** @var UserExtCamel $ext */
        $ext = UserExtCamel::query()->find(1);
        $this->assertArrayHasKey('floatNum', $ext->toArray());
        $this->assertArrayHasKey('createdAt', $ext->toArray());
        $this->assertIsString($ext->updatedAt);
        $this->assertIsString($ext->toArray()['updatedAt']);

        $this->assertIsString($number = $ext->floatNum);

        $ext->increment('float_num', 1);

        $model = UserExtCamel::query()->find(1);
        $this->assertSame($ext->floatNum, $model->floatNum);

        $model->fill([
            'floatNum' => '1.20',
        ]);
        $model->save();

        $sqls = [
            'select * from `user_ext` where `user_ext`.`id` = ? limit 1',
            'update `user_ext` set `float_num` = `float_num` + 1, `user_ext`.`updated_at` = ? where `id` = ?',
            'select * from `user_ext` where `user_ext`.`id` = ? limit 1',
            'update `user_ext` set `float_num` = ?, `user_ext`.`updated_at` = ? where `id` = ?',
        ];
        while ($event = $this->channel->pop(0.001)) {
            if ($event instanceof QueryExecuted) {
                $this->assertSame($event->sql, array_shift($sqls));
            }
        }
    }

    public function testSaveMorphPivot()
    {
        $this->getContainer();
        $pivot = UserRoleMorphPivot::query()->find(1);
        $pivot->created_at = $now = Carbon::now();
        $pivot->save();

        $sqls = [
            ['select * from `user_role` where `user_role`.`id` = ? limit 1', [1]],
            ['update `user_role` set `created_at` = ?, `user_role`.`updated_at` = ? where `id` = ?', [$now->toDateTimeString(), $now->toDateTimeString(), 1]],
        ];

        while ($event = $this->channel->pop(0.001)) {
            if ($event instanceof QueryExecuted) {
                $this->assertSame([$event->sql, $event->bindings], array_shift($sqls));
            }
        }
    }

    public function testGetColumnListing()
    {
        $container = $this->getContainer();
        $connection = $container->get(ConnectionResolverInterface::class)->connection();
        /** @var MySqlBuilder $builder */
        $builder = $connection->getSchemaBuilder('default');
        $columns = $builder->getColumnListing('user_ext');
        foreach ($columns as $column) {
            $this->assertSame($column, strtolower($column));
        }
    }

    public function testGetColumnTypeListing()
    {
        $container = $this->getContainer();
        $connection = $container->get(ConnectionResolverInterface::class)->connection();
        /** @var MySqlBuilder $builder */
        $builder = $connection->getSchemaBuilder('default');
        $columns = $builder->getColumnTypeListing('user_ext');
        $column = $columns[0];
        foreach ($column as $key => $value) {
            $this->assertSame($key, strtolower($key));
        }
    }

    public function testGetColumns()
    {
        $container = $this->getContainer();
        $connection = $container->get(ConnectionResolverInterface::class)->connection();
        /** @var MySqlBuilder $builder */
        $builder = $connection->getSchemaBuilder('default');
        $columns = $builder->getColumns();
        foreach ($columns as $column) {
            if ($column->getTable() === 'book') {
                break;
            }
        }
        $this->assertInstanceOf(Column::class, $column);
        $this->assertSame('hyperf', $column->getSchema());
        $this->assertSame('book', $column->getTable());
        $this->assertSame('id', $column->getName());
        $this->assertSame(1, $column->getPosition());
        $this->assertSame(null, $column->getDefault());
        $this->assertSame(false, $column->isNullable());
        $this->assertSame('bigint', $column->getType());
        $this->assertSame('', $column->getComment());
    }

    public function testBigIntInsertAndGet()
    {
        $container = $this->getContainer();
        /** @var ConnectionInterface $conn */
        $conn = $container->get(ConnectionResolverInterface::class)->connection();
        $conn->statement('CREATE TABLE `test` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `uid` bigint(20) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');

        $sql = 'INSERT INTO test(`user_id`, `uid`) VALUES (?,?)';
        $this->assertTrue($conn->insert($sql, [PHP_INT_MAX, 1]));

        $binds = [
            [PHP_INT_MAX, 1],
            [(string) PHP_INT_MAX, 1],
            [PHP_INT_MAX, (string) 1],
            [(string) PHP_INT_MAX, (string) 1],
        ];
        $sql = 'SELECT * FROM test WHERE user_id = ? AND uid = ?';
        foreach ($binds as $bind) {
            $res = $conn->select($sql, $bind);
            $this->assertNotEmpty($res);
        }

        $binds = [
            [1, PHP_INT_MAX],
            [1, (string) PHP_INT_MAX],
            [(string) 1, PHP_INT_MAX],
            [(string) 1, (string) PHP_INT_MAX],
        ];
        $sql = 'SELECT * FROM test WHERE uid = ? AND user_id = ?';
        foreach ($binds as $bind) {
            $res = $conn->select($sql, $bind);
            $this->assertNotEmpty($res);
        }
    }

    public function testPaginationCountQuery()
    {
        $container = $this->getContainer();
        $container->shouldReceive('make')->with(LengthAwarePaginatorInterface::class, Mockery::any())->andReturnUsing(function ($_, $args) {
            return new LengthAwarePaginator(...array_values($args));
        });
        User::query()->select('gender')->groupBy('gender')->paginate(10, ['*'], 'page', 0);
        $sqls = [
            'select count(*) as aggregate from (select `gender` from `user` group by `gender`) as `aggregate_table`',
            'select `gender` from `user` group by `gender` limit 10 offset 0',
        ];
        while ($event = $this->channel->pop(0.001)) {
            if ($event instanceof QueryExecuted) {
                $this->assertSame($event->sql, array_shift($sqls));
            }
        }
    }

    protected function getContainer()
    {
        $dispatcher = Mockery::mock(EventDispatcherInterface::class);
        $dispatcher->shouldReceive('dispatch')->with(Mockery::any())->andReturnUsing(function ($event) {
            $this->channel->push($event);
        });
        $container = ContainerStub::getContainer(function ($conn) use ($dispatcher) {
            $conn->setEventDispatcher($dispatcher);
        });
        $container->shouldReceive('get')->with(EventDispatcherInterface::class)->andReturn($dispatcher);

        return $container;
    }
}
