<?php

namespace Terrazza\Annotation\ReflectionTypes;

use ReflectionClass;
use ReflectionProperty;
use ReflectionUnionType;
use Terrazza\Annotation\ReflectionClassNameResolverInterface;
use Terrazza\Annotation\ReflectionTypesBuilderInterface;

class ReflectionTypesBuilder implements ReflectionTypesBuilderInterface {
    private ReflectionClassNameResolverInterface $classNameResolver;
    public function __construct(ReflectionClassNameResolverInterface $classNameResolver) {
        $this->classNameResolver                        = $classNameResolver;
    }

    CONST nullablePattern                               = "null";
    CONST arrayPattern                                  = "[]";
    public function getTypes(ReflectionProperty $property) : ReflectionUnionTypes {
        $types                                          = [];
        $typesNullable                                  = false;
        if ($annotation = $this->getAnnotation('@var\s+([^\s]+)', $property->getDocComment())) {
            $annotations                                = explode("|", $annotation);
            foreach ($annotations as $annotation) {
                if ($this->isNullableTypeName($annotation)) {
                    $typesNullable                      = true;
                    continue;
                }
                $isMultiple                             = $this->isArrayTypeName($annotation);
                $annotation                             = $this->cleanTypeName($annotation);
                $types[]                                = new ReflectionType($annotation, $isMultiple);;
            }
        } else {
            $type                                       = $property->getType();
            if ($type instanceof ReflectionUnionType) {
                $typesNullable                          = $type->allowsNull();
                foreach ($type->getTypes() as $unionType) {
                    $typeName                           = $unionType->getName();
                    if ($this->isNullableTypeName($typeName)) {
                        $typesNullable                  = true;
                        continue;
                    }
                    $types[]                            = new ReflectionType($typeName);
                }
            } else {
                $typeName                               = $type->getName();
                $types[]                                = new ReflectionType($typeName);
            }
        }
        foreach ($types as &$type) {
            $type                                       = $this->resolveClassNames($property->getDeclaringClass(), $type);
        }
        return new ReflectionUnionTypes($typesNullable, ...$types);
    }

    /**
     * @param ReflectionClass $parentClass
     * @param ReflectionType $type
     * @return ReflectionType
     */
    private function resolveClassNames(ReflectionClass $parentClass, ReflectionType $type) : ReflectionType {
        if (!$type->isBuiltin()) {
            $parentClassName                            = $parentClass->getName();
            $findClassName                              = $type->getName();
            if ($foundClassName = $this->classNameResolver->getClassName($parentClassName, $findClassName)) {
                $type                                   = $type->withType($foundClassName);
            }
        }
        return $type;
    }
    private function isNullableTypeName(string $typeName) : bool {
        return self::nullablePattern === $typeName;
    }
    private function isArrayTypeName(string $typeName) : bool {
        return (strpos($typeName, self::arrayPattern) !== false);
    }
    private function cleanTypeName(string $typeName) : string {
        return str_replace(self::arrayPattern, "",$typeName);
    }

    /**
     * @param string $pattern
     * @param string $docComment
     * @return string|null
     */
    private function getAnnotation(string $pattern, string $docComment) :?string {
        if (preg_match('/' . $pattern. '/', $docComment, $matches)) {
            return $matches[1];
        } else {
            return null;
        }
    }
}