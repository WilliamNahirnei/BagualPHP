<?php

namespace Server\Suport;

trait TraitSuportValidationClass {
    private function classExists(string $class): void {
        if (!class_exists($class)) {
            throw new \Exception("Class '{$class}' not found");
        }
    }

    private function classExtends(string $class, string $expectedParentClass): void {
        if (!is_subclass_of($class, $expectedParentClass)) {
            throw new \Exception("Class '{$class}' must extend '{$expectedParentClass}'");
        }
    }

    private function methodExists(string $class, string $method): void {
        if (!method_exists($class, $method)) {
            throw new \Exception("Method '{$method}' not found in class '{$class}'");
        }
    }

    private function methodIsStatic(string $class, string $method): void {
        $reflectionMethod = new \ReflectionMethod($class, $method);
        if (!$reflectionMethod->isStatic()) {
            throw new \Exception("Method '{$method}' in class '{$class}' must be static");
        }
    }

    private function methodHasNoParameters(string $class, string $method): void {
        $reflectionMethod = new \ReflectionMethod($class, $method);
        if ($reflectionMethod->getNumberOfParameters() > 0) {
            throw new \Exception("Method '{$method}' in class '{$class}' must not have parameters");
        }
    }
}
?>