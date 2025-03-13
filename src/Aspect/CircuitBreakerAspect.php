<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\Aspect;

use Hyperf\CircuitBreaker\Annotation\CircuitBreaker;
use Hyperf\CircuitBreaker\CircuitBreakerInterface;
use Hyperf\CircuitBreaker\Handler\AbstractHandler;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Multiplex\Exception\RuntimeException;
use Psr\Http\Client\ClientExceptionInterface;

class CircuitBreakerAspect extends AbstractAspect
{
    public array $classes = [
        AbstractHandler::class . '::call',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        try {
            return $proceedingJoinPoint->process();
        } catch (ClientExceptionInterface|RuntimeException $exception) {
            /** @var CircuitBreakerInterface $breaker */
            $breaker = $proceedingJoinPoint->arguments['keys']['breaker'];
            $breaker->incrFailCounter();

            /** @var CircuitBreaker $breaker */
            $annotation = $proceedingJoinPoint->arguments['keys']['annotation'];

            /** @var AbstractHandler $instance */
            $instance = $proceedingJoinPoint->getInstance();
            (fn () => $this->switch($breaker, $annotation, false))->call($instance);

            throw new $exception();
        }
    }
}
