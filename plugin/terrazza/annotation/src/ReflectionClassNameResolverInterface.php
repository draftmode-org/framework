<?php
namespace Terrazza\Annotation;
use RuntimeException;

interface ReflectionClassNameResolverInterface {
    /**
     * @param string $parentClass
     * @param class-string<T> $findClass
     * @return T|null
     * @template T of object
     * @throws RuntimeException
     */
    public function getClassName(string $parentClass, string $findClass): ?string;
}