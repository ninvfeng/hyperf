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
namespace HyperfTest\HttpServer\Stub;

use Hyperf\HttpServer\Server;

class ServerStub extends Server
{
    public function initRequestAndResponse($request, $response): array
    {
        return parent::initRequestAndResponse($request, $response);
    }
}
