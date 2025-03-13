<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker;

use Hyperf\Contract\ConfigInterface;

class DowngradeConfig implements DowngradeConfigInterface
{
    public function __construct(private readonly ConfigInterface $config)
    {
    }

    public function isDowngrading(): bool
    {
        return $this->config->get('foundation.circuit_breaker.downgrade.enable', false);
    }
}
