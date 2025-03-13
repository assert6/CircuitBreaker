<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\BreakerHandler;

use Closure;
use Hyperf\CircuitBreaker\Annotation\CircuitBreaker as Annotation;
use Hyperf\Context\ApplicationContext;
use Psr\SimpleCache\CacheInterface;
use Assert6\CircuitBreaker\CircuitBreaker;
use Assert6\CircuitBreaker\DowngradeHandler\CacheHandler;
use Assert6\CircuitBreaker\DowngradeHandler\DowngradeHandlerInterface;
use Assert6\CircuitBreaker\DowngradeHandler\ExceptionRejectHandler;
use Assert6\CircuitBreaker\DowngradeHandler\FakeHandler;
use Assert6\CircuitBreaker\DowngradeHandler\TemplateHandler;

/**
 * @Annotation
 */
class Timeout
{
    public static function fake(
        int $successCounter = 10,
        int $failCounter = 10,
        float $timeout = 3,
        ?Closure $closure = null,
        ...$parameter,
    ): mixed {
        $callback = new FakeHandler($closure, ...$parameter);

        return self::process($callback, $successCounter, $failCounter, $timeout);
    }

    public static function throw(
        int $successCounter = 10,
        int $failCounter = 10,
        float $timeout = 3,
        ?Closure $closure = null,
        ...$parameter,
    ): mixed {
        $callback = new ExceptionRejectHandler($closure, ...$parameter);

        return self::process($callback, $successCounter, $failCounter, $timeout);
    }

    public static function template(
        mixed $template,
        int $successCounter = 10,
        int $failCounter = 10,
        float $timeout = 3,
        ?Closure $closure = null,
        ...$parameter,
    ): mixed {
        $callback = new TemplateHandler($template, $closure, ...$parameter);

        return self::process($callback, $successCounter, $failCounter, $timeout);
    }

    public static function cache(
        string $cacheKey,
        int $successCounter = 10,
        int $failCounter = 10,
        float $timeout = 3,
        ?Closure $closure = null,
        ...$parameter,
    ): mixed {
        $cache = ApplicationContext::getContainer()->get(CacheInterface::class);
        $callback = new CacheHandler($cache, $cacheKey, $closure, ...$parameter);

        return self::process($callback, $successCounter, $failCounter, $timeout);
    }

    private static function process(
        DowngradeHandlerInterface $handler,
        int $successCounter = 10,
        int $failCounter = 10,
        float $timeout = 3,
    ): mixed {
        $annotation = new Annotation(
            successCounter: $successCounter,
            failCounter: $failCounter,
            options: ['timeout' => $timeout],
        );
        return (new CircuitBreaker($annotation, $handler))->process();
    }
}
