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

/**
 * 不处理.
 */
class NoopHandler extends AbstractDowngradeHandler
{
    public function fallback(ProceedingJoinPoint $proceedingJoinPoint): mixed
    {
        return $proceedingJoinPoint->process();
    }
}
