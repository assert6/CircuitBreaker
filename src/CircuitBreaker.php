<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker;

use Closure;
use Hyperf\CircuitBreaker\Annotation\CircuitBreaker as Annotation;
use Hyperf\CircuitBreaker\Handler\HandlerInterface;
use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use ReflectionFunction;
use Assert6\CircuitBreaker\DowngradeHandler\DowngradeHandlerInterface;
use Assert6\CircuitBreaker\Exception\InvalidArgumentException;

class CircuitBreaker
{
    protected HandlerInterface $handler;

    protected string $className;

    protected string $method;

    public function __construct(
        protected Annotation $annotation,
        protected DowngradeHandlerInterface $downgradeHandler,
    ) {
        $this->annotation->fallback = $this->downgradeHandler->fallback(...);

        /* @var HandlerInterface $handler */
        $this->handler = ApplicationContext::getContainer()->get($annotation->handler);

        [$this->className, $this->method] = self::getClassAndMethod($this->downgradeHandler->closure);
    }

    public function process()
    {
        $annotation = $this->annotation;

        $proceedingJoinPoint = new ProceedingJoinPoint($this->downgradeHandler->process(...), $this->className, $this->method, ['keys' => []]);
        $proceedingJoinPoint->pipe = $proceedingJoinPoint->processOriginalMethod(...);

        return $this->handler->handle($proceedingJoinPoint, $annotation);
    }

    private static function getClassAndMethod(Closure $closure): array
    {
        $reflection = new ReflectionFunction($closure);
        if (! $className = $reflection->getClosureScopeClass()?->getName()) {
            throw new InvalidArgumentException('$closure must be a Class Method');
        }
        return [$className, $reflection->getName()];
    }
}
