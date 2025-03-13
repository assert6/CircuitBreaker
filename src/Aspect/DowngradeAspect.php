<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\Aspect;

use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Assert6\CircuitBreaker\Annotation\Downgradable;
use Assert6\CircuitBreaker\DowngradeConfigInterface;
use Assert6\CircuitBreaker\Exception\CircuitBreakerRejectException;

class DowngradeAspect extends AbstractAspect
{
    public array $annotations = [
        Downgradable::class,
    ];

    public function __construct(protected DowngradeConfigInterface $config)
    {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        if ($this->config->isDowngrading()) {
            throw new CircuitBreakerRejectException($proceedingJoinPoint->className, $proceedingJoinPoint->methodName);
        }
        return $proceedingJoinPoint->process();
    }
}
