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
namespace HyperfTest\ExceptionHandler;

use Hyperf\ExceptionHandler\Formatter\DefaultFormatter;
use PHPStan\Testing\TestCase;

/**
 * @internal
 * @coversNothing
 */
class FormatterTest extends TestCase
{
    public function testDefaultFormatter()
    {
        $formatter = new DefaultFormatter();

        $message = uniqid();
        $code = rand(1000, 9999);
        $exception = new \RuntimeException($message, $code);
        $expected = str_replace($message, $message . "({$code})", (string) $exception);
        $this->assertSame($expected, $formatter->format($exception));
    }
}
