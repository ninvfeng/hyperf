# v2.1.6 - TBD

## Fixed

- [#3233](https://github.com/hyperf/hyperf/pull/3233) Fixed connection exhausted, when connect amqp server failed.

## Removed

- [#3235](https://github.com/hyperf/hyperf/pull/3235) Removed rebalance check, because `longlang/phpkafka` checked.

# v2.1.5 - 2021-02-01

## Fixed

- [#3204](https://github.com/hyperf/hyperf/pull/3204) Fixed unexpected behavior for `middlewares` when using `rpc-server`.
- [#3209](https://github.com/hyperf/hyperf/pull/3209) Fixed bug that connection was not be released to pool when the amqp consumer broken in coroutine style server.
- [#3222](https://github.com/hyperf/hyperf/pull/3222) Fixed memory leak for join queries in `hyperf/database`.
- [#3228](https://github.com/hyperf/hyperf/pull/3228) Fixed bug that server crash when tracer flush failed in defer.
- [#3230](https://github.com/hyperf/hyperf/pull/3230) Fixed `orderBy` does not works for `hyperf/scout`.

## Added

- [#3211](https://github.com/hyperf/hyperf/pull/3211) Added optional configuration url for nacos which used to request nacos server.
- [#3214](https://github.com/hyperf/hyperf/pull/3214) Added Caller which help you to use instance in coroutine security mode.
- [#3224](https://github.com/hyperf/hyperf/pull/3224) Added `Hyperf\Utils\CodeGen\Package::getPrettyVersion()`.

## Changed

- [#3218](https://github.com/hyperf/hyperf/pull/3218) Set qos of amqp by default.
- [#3224](https://github.com/hyperf/hyperf/pull/3224) Upgrade `jean85/pretty-package-versions` to `^1.2|^2.0`, which support `composer 2.x`.

## Optimized

- [#3226](https://github.com/hyperf/hyperf/pull/3226) Run pagination count as subquery for group by and havings.

# v2.1.4 - 2021-01-25

## Fixed

- [#3165](https://github.com/hyperf/hyperf/pull/3165) Fixed `Hyperf\Database\Schema\MySqlBuilder::getColumnListing` does not works in `MySQL 8.0`.
- [#3174](https://github.com/hyperf/hyperf/pull/3174) Fixed bug that the where bindings will be replaced by not rigorous code.
- [#3179](https://github.com/hyperf/hyperf/pull/3179) Fixed json-rpc client failed to receive data when the target server restart.
- [#3189](https://github.com/hyperf/hyperf/pull/3189) Fixed kafka producer unusable in cluster setup.
- [#3191](https://github.com/hyperf/hyperf/pull/3191) Fixed rpc-client with pool transporter recv failed once when the server restart in the next request.

## Added

- [#3170](https://github.com/hyperf/hyperf/pull/3170) Added `FindNewerDriver` which is friendly with mac, linux and docker for watcher.
- [#3195](https://github.com/hyperf/hyperf/pull/3195) Added `retry_count` for JsonRpcPoolTransporter, the default retry count is 2.

## Optimized

- [#3169](https://github.com/hyperf/hyperf/pull/3169) Optimized code for `set_error_handler` of `ErrorExceptionHandler`, which expects `callable(int, string, string, int, array): bool`.
- [#3191](https://github.com/hyperf/hyperf/pull/3191) Optimized code for `hyperf/json-rpc`, try to reconnect the server when connection closed.

## Changed

- [#3174](https://github.com/hyperf/hyperf/pull/3174) Assert the binding values for database by default.

## Incubator

- [DAG](https://github.com/hyperf/dag-incubator) Directed Acyclic Graph.
- [RPN](https://github.com/hyperf/rpn-incubator) Reverse Polish Notation.

# v2.1.3 - 2021-01-18

## Fixed

- [#3070](https://github.com/hyperf/hyperf/pull/3070) Fixed `tracer` does not works in hyperf `v2.1`.
- [#3106](https://github.com/hyperf/hyperf/pull/3106) Fixed bug that call to a member function getArrayCopy() on null when the parent coroutine context destroyed.
- [#3108](https://github.com/hyperf/hyperf/pull/3108) Fixed routes will be replaced by another group when using `describe:routes` command.
- [#3118](https://github.com/hyperf/hyperf/pull/3118) Fixed bug that the config key of migrations is not correct.
- [#3126](https://github.com/hyperf/hyperf/pull/3126) Fixed bug that swoole v4.6 `SWOOLE_HOOK_SOCKETS` conflicts with jaeger tracing.
- [#3137](https://github.com/hyperf/hyperf/pull/3137) Fixed type hint error, when don't set `true` for `PDO::ATTR_PERSISTENT`.
- [#3141](https://github.com/hyperf/hyperf/pull/3141) Fixed `doctrine/dbal` does not works when using migration.

## Added

- [#3059](https://github.com/hyperf/hyperf/pull/3059) The merged attributes in the view component support attributes other than 'class'.
- [#3123](https://github.com/hyperf/hyperf/pull/3123) Added method `ComponentAttributeBag::has()` for `view-engine`.

# v2.1.2 - 2021-01-11

## Fixed

- [#3050](https://github.com/hyperf/hyperf/pull/3050) Fixed extra data saved twice when use `save()` after `increment()` with `extra`.
- [#3082](https://github.com/hyperf/hyperf/pull/3082) Fixed connection has already been bound to another coroutine when used in defer for `hyperf/db`.
- [#3084](https://github.com/hyperf/hyperf/pull/3084) Fixed `getRealPath` does not works in phar.
- [#3087](https://github.com/hyperf/hyperf/pull/3087) Fixed memory leak when using pipeline sometimes.
- [#3095](https://github.com/hyperf/hyperf/pull/3095) Fixed unexpected behavior for `ElasticsearchEngine::getTotalCount()` in `hyperf/scout`.

## Added

- [#2847](https://github.com/hyperf/hyperf/pull/2847) Added `hyperf/kafka` component.
- [#3066](https://github.com/hyperf/hyperf/pull/3066) Added method `ConnectionInterface::run(Closure $closure)` for `hyperf/db`.

## Optimized

- [#3046](https://github.com/hyperf/hyperf/pull/3046) Optimized `phar:build` for rewriting `scan_cacheable`.

## Changed

- [#3077](https://github.com/hyperf/hyperf/pull/3077) Reduced `league/flysystem` to `^1.0`.

# v2.1.1 - 2021-01-04

## Fixed

- [#3045](https://github.com/hyperf/hyperf/pull/3045) Fixed type hint error, when don't set `true` for `PDO::ATTR_PERSISTENT`.
- [#3047](https://github.com/hyperf/hyperf/pull/3047) Fixed bug that renew sid in all namespaces failed.
- [#3062](https://github.com/hyperf/hyperf/pull/3062) Fixed bug that parameters don't parsed correctly in grpc server.

## Added

- [#3052](https://github.com/hyperf/hyperf/pull/3052) Support collecting metrics while running command.
- [#3054](https://github.com/hyperf/hyperf/pull/3054) Support `Engine::close` protocol and improve error handling for `socketio-server`.

# v2.1.0 - 2020-12-28

## Dependencies Upgrade

- Upgraded `php` to `>=7.3`;
- Upgraded `phpunit/phpunit` to `^9.0`;
- Upgraded `guzzlehttp/guzzle` to `^6.0|^7.0`;
- Upgraded `vlucas/phpdotenv` to `^5.0`;
- Upgraded `endclothing/prometheus_client_php` to `^1.0`;
- Upgraded `twig/twig` to `^3.0`;
- Upgraded `jcchavezs/zipkin-opentracing` to `^0.2.0`;
- Upgraded `doctrine/dbal` to `^3.0`;
- Upgraded `league/flysystem` to `^1.0|^2.0`;

## Removed

- Removed deprecated property `$name` from `Hyperf\Amqp\Builder`.
- Removed deprecated method `consume` from `Hyperf\Amqp\Message\ConsumerMessageInterface`.
- Removed deprecated property `$running` from `Hyperf\AsyncQueue\Driver\Driver`.
- Removed deprecated method `parseParameters` from `Hyperf\HttpServer\CoreMiddleware`.
- Removed deprecated const `ON_WORKER_START` and `ON_WORKER_EXIT` from `Hyperf\Utils\Coordinator\Constants`.
- Removed deprecated method `get` from `Hyperf\Utils\Coordinator`.
- Removed config `rate-limit.php`, please use `rate_limit.php` instead.
- Removed useless class `Hyperf\Resource\Response\ResponseEmitter`.
- Removed component `hyperf/paginator` from database's dependencies.
- Removed method `stats` from `Hyperf\Utils\Coroutine\Concurrent`.

## Changed

- `Hyperf\Utils\Coroutine::parentId` which returns the parent coroutine ID
  * Returns 0 when running in the top level coroutine.
  * Throws RunningInNonCoroutineException when running in non-coroutine context
  * Throws CoroutineDestroyedException when the coroutine has been destroyed

- `Hyperf\Guzzle\CoroutineHandler`
  * Deleted method `execute`
  * Method `initHeaders` will return `$headers`, instead of assigning "$headers" directly to the client.
  * Deleted method `checkStatusCode`

- [#2720](https://github.com/hyperf/hyperf/pull/2720) Don't set `data_type` for `PDOStatement::bindValue`.
- [#2871](https://github.com/hyperf/hyperf/pull/2871) Use `(string) $body` instead of `$body->getContents()` for getting contents from `StreamInterface`, because method `getContents()` only returns the remaining contents in a string.
- [#2909](https://github.com/hyperf/hyperf/pull/2909) Allow setting repeated middlewares.
- [#2935](https://github.com/hyperf/hyperf/pull/2935) Changed the string format for default exception formatter.
- [#2979](https://github.com/hyperf/hyperf/pull/2979) Don't format `decimal` to `float` for command `gen:model` by default.

## Deprecated

- `Hyperf\AsyncQueue\Signal\DriverStopHandler` will be deprecated in v2.2, please use `Hyperf\Process\Handler\ProcessStopHandler` instead.
- `Hyperf\Server\SwooleEvent` will be deprecated in v3.0, please use `Hyperf\Server\Event` instead.

## Added

- [#2659](https://github.com/hyperf/hyperf/pull/2659) [#2663](https://github.com/hyperf/hyperf/pull/2663) Support `HttpServer` for [Swow](https://github.com/swow/swow).
- [#2671](https://github.com/hyperf/hyperf/pull/2671) Added `Hyperf\AsyncQueue\Listener\QueueHandleListener` which can record running logs for async-queue.
- [#2923](https://github.com/hyperf/hyperf/pull/2923) Added `Hyperf\Utils\Waiter` which can wait coroutine to end.
- [#3001](https://github.com/hyperf/hyperf/pull/3001) Added method `Hyperf\Database\Model\Collection::columns()`.
- [#3002](https://github.com/hyperf/hyperf/pull/3002) Added params `$depth` and `$flags` for `Json::decode` and `Json::encode`.

## Fixed

- [#2741](https://github.com/hyperf/hyperf/pull/2741) Fixed bug that process does not works in swow server.

## Optimized

- [#3009](https://github.com/hyperf/hyperf/pull/3009) Optimized code for prometheus which support `https` not only `http`.
