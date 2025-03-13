<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\DowngradeHandler;

use Hyperf\Di\Aop\ProceedingJoinPoint;
use Assert6\CircuitBreaker\Exception\CircuitBreakerRejectException;

/**
 * 异常拒绝.
 */
class ExceptionRejectHandler extends AbstractDowngradeHandler
{
    public function fallback(ProceedingJoinPoint $proceedingJoinPoint): mixed
    {
        [$className, $method] = self::getClassAndMethod($this->closure);
        throw new CircuitBreakerRejectException($className, $method);
    }
}
