<?php

namespace Assert6\CircuitBreaker;

use Hyperf\Di\Aop\AstVisitorRegistry;

class ConfigProvider
{
    public function __invoke()
    {
        if (!AstVisitorRegistry::exists(AST\CircuitBreakerVisitor::class)) {
            AstVisitorRegistry::insert(AST\CircuitBreakerVisitor::class);
        }

        return [
            'aspects' => [
                Aspect\CircuitBreakerAspect::class,
                Aspect\BreakerOpenAspect::class,
                Aspect\DowngradeAspect::class,
            ],
            'dependencies' => [
                DowngradeConfigInterface::class => DowngradeConfig::class,
            ]
        ];
    }
}