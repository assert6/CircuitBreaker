<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Downgradable extends AbstractAnnotation
{
}
