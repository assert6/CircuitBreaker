<?php

declare(strict_types=1);
/**
 * This file is part of tw591pk/service-foundation.
 *
 * @link     https://code.addcn.com/tw591pk/service-foundation
 * @contact  hdj@addcn.com
 */

namespace Assert6\CircuitBreaker\DowngradeHandler;

use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\ReflectionManager;
use ReflectionUnionType;
use Assert6\CircuitBreaker\Exception\InvalidReturnTypeException;

/**
 * 虚假响应.
 */
class FakeHandler extends AbstractDowngradeHandler
{
    public function fallback(ProceedingJoinPoint $proceedingJoinPoint): mixed
    {
        $returnType = ReflectionManager::reflectMethod($proceedingJoinPoint->className, $proceedingJoinPoint->methodName)->getReturnType();

        if ($returnType instanceof ReflectionUnionType) {
            $returnType = $returnType->getTypes()[0];
        }
        $type = (string) $returnType;
        switch (true) {
            case is_null($returnType) || $returnType->allowsNull():
                return null;
            case $type === 'array':
                return [];
            case $returnType->isBuiltin():
                $result = false;
                settype($result, $type);
                return $result;
            default:
                throw new InvalidReturnTypeException("Invalid ReturnType {$type} in {$proceedingJoinPoint->className}::{$proceedingJoinPoint->methodName}");
        }
    }
}
