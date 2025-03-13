<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\DowngradeHandler;

use Closure;
use Hyperf\Cache\Helper\StringHelper;
use Hyperf\Coroutine\Coroutine;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Psr\SimpleCache\CacheInterface;

/**
 * 兜底缓存.
 */
class CacheHandler extends AbstractDowngradeHandler
{
    public function __construct(protected CacheInterface $cache, protected string $cacheKey, Closure $closure, ...$parameter)
    {
        parent::__construct($closure, ...$parameter);

        $this->cacheKey = $this->getCacheKey($cacheKey);
    }

    public function process(): mixed
    {
        $result = parent::process();
        Coroutine::create(fn () => $this->cache->set($this->cacheKey, $result, 24 * 60 * 60));
        return $result;
    }

    public function fallback(ProceedingJoinPoint $proceedingJoinPoint): mixed
    {
        return $this->cache->get($this->cacheKey);
    }

    private function getCacheKey(string $cacheKey): string
    {
        [$className, $method] = self::getClassAndMethod($this->closure);
        $arguments = self::getMethodParamsMap($className, $method, $this->parameter);

        return StringHelper::format('CircuitBreaker:cache', $arguments['keys'], $cacheKey);
    }
}
