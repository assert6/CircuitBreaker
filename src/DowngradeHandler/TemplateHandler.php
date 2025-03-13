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
use Hyperf\Di\Aop\ProceedingJoinPoint;

use function Hyperf\Support\value;

/**
 * 样板数据.
 */
class TemplateHandler extends AbstractDowngradeHandler
{
    public function __construct(protected mixed $template, Closure $closure, ...$parameter)
    {
        parent::__construct($closure, ...$parameter);
    }

    public function fallback(ProceedingJoinPoint $proceedingJoinPoint): mixed
    {
        return value($this->template, ...$this->parameter);
    }
}
