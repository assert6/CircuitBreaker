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
use Hyperf\Di\ReflectionManager;
use ReflectionFunction;
use Assert6\CircuitBreaker\Exception\InvalidArgumentException;

abstract class AbstractDowngradeHandler implements DowngradeHandlerInterface
{
    protected array $parameter = [];

    public function __construct(public Closure $closure, ...$parameter)
    {
        $this->parameter = $parameter;
    }

    public function process(): mixed
    {
        return ($this->closure)(...$this->parameter);
    }

    protected static function getClassAndMethod(Closure $closure): array
    {
        $reflection = new ReflectionFunction($closure);
        if (! $className = $reflection->getClosureScopeClass()?->getName()) {
            throw new InvalidArgumentException('$closure must be a Class Method');
        }
        return [$className, $reflection->getName()];
    }

    protected static function getMethodParamsMap(string $className, string $method, array $params): array
    {
        $reflectParameters = ReflectionManager::reflectMethod($className, $method)->getParameters();
        $leftParamCount = count($params);
        $arguments = [
            'order' => [],
            'keys' => [],
            'variadic' => '',
        ];
        foreach ($reflectParameters as $reflectParameter) {
            if ($leftParamCount <= 0) {
                break;
            }
            --$leftParamCount;

            $paramName = $reflectParameter->getName();

            $arguments['order'][] = $paramName;

            if ($params[$paramName] ?? false) {
                $arguments['keys'][$paramName] = $params[$paramName];
                unset($params[$paramName]);
                continue;
            }

            if ($reflectParameter->isVariadic()) {
                $arguments['keys'][$paramName] = $params;
                $arguments['variadic'] = $paramName;
            } else {
                $arguments['keys'][$paramName] = array_shift($params);
            }
        }
        return $arguments;
    }
}
