<?php

namespace Server\Suport;

/**
 * Trait TraitSuportValidationClass
 * 
 * Provides various validation methods to check the existence, inheritance, and characteristics of classes and methods.
 * 
 * @package Server\Suport
 */
trait TraitSuportValidationClass {
    /**
     * Validate that a class exists.
     *
     * @param string $class The fully qualified name of the class to check.
     * @throws \Exception if the class does not exist.
     */
    private function classExists(string $class): void {
        if (!class_exists($class)) {
            throw new \Exception("Class '{$class}' not found");
        }
    }

    /**
     * Validate that a class extends a specific parent class.
     *
     * @param string $class The fully qualified name of the class to check.
     * @param string $expectedParentClass The fully qualified name of the expected parent class.
     * @throws \Exception if the class does not extend the expected parent class.
     */
    private function classExtends(string $class, string $expectedParentClass): void {
        if (!is_subclass_of($class, $expectedParentClass)) {
            throw new \Exception("Class '{$class}' must extend '{$expectedParentClass}'");
        }
    }

    /**
     * Validate that a method exists in a class.
     *
     * @param string $class The fully qualified name of the class to check.
     * @param string $method The name of the method to check.
     * @throws \Exception if the method does not exist in the class.
     */
    private function methodExists(string $class, string $method): void {
        if (!method_exists($class, $method)) {
            throw new \Exception("Method '{$method}' not found in class '{$class}'");
        }
    }

    /**
     * Validate that a method in a class is static.
     *
     * @param string $class The fully qualified name of the class to check.
     * @param string $method The name of the method to check.
     * @throws \Exception if the method is not static.
     */
    private function methodIsStatic(string $class, string $method): void {
        $reflectionMethod = new \ReflectionMethod($class, $method);
        if (!$reflectionMethod->isStatic()) {
            throw new \Exception("Method '{$method}' in class '{$class}' must be static");
        }
    }

    /**
     * Validate that a method in a class has no parameters.
     *
     * @param string $class The fully qualified name of the class to check.
     * @param string $method The name of the method to check.
     * @throws \Exception if the method has parameters.
     */
    private function methodHasNoParameters(string $class, string $method): void {
        $reflectionMethod = new \ReflectionMethod($class, $method);
        if ($reflectionMethod->getNumberOfParameters() > 0) {
            throw new \Exception("Method '{$method}' in class '{$class}' must not have parameters");
        }
    }
}
?>