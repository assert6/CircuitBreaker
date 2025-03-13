<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\Exception;

use Exception;
use Throwable;

class CircuitBreakerRejectException extends Exception
{
    public function __construct(string $className, string $method, ?Throwable $previous = null)
    {
        parent::__construct("CircuitBreaker reject call to {$className}::{$method}", previous: $previous);
    }
}
