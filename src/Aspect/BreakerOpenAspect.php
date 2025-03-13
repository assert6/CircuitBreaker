<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\Aspect;

use Hyperf\CircuitBreaker\CircuitBreaker;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Sentry\SentrySdk;
use Throwable;
use Assert6\CircuitBreaker\Exception\CircuitBreakerRejectException;

class BreakerOpenAspect extends AbstractAspect
{
    public array $classes = [
        CircuitBreaker::class . '::open',
    ];

    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /** @var CircuitBreaker $instance */
        $instance = $proceedingJoinPoint->getInstance();

        $name = (fn () => $this->name)->call($instance);
        [$className, $method] = explode('::', $name);

        $hub = SentrySdk::getCurrentHub();
        try {
            $hub->captureException(new CircuitBreakerRejectException($className, $method));
        } catch (Throwable $e) {
            $this->logger->error((string) $e);
        }
        return $proceedingJoinPoint->process();
    }
}
