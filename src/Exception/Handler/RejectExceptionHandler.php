<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\Exception\Handler;

use Hyperf\Codec\Json;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;
use Assert6\CircuitBreaker\Exception\CircuitBreakerRejectException;

class RejectExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        $this->stopPropagation();
        return $response->withBody(new SwooleStream(Json::encode([
            'status' => 503,
            'msg' => '稍後再試',
            'data' => [],
        ])));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof CircuitBreakerRejectException;
    }
}
