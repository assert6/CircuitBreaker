<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\DowngradeHandler;

use Hyperf\CircuitBreaker\FallbackInterface;

interface DowngradeHandlerInterface extends FallbackInterface
{
    public function process(): mixed;
}
