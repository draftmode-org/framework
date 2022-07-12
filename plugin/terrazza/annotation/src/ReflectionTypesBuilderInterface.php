<?php
namespace Terrazza\Annotation;
use ReflectionProperty;
use Terrazza\Annotation\ReflectionTypes\ReflectionUnionTypes;

interface ReflectionTypesBuilderInterface {
    public function getTypes(ReflectionProperty $property) : ReflectionUnionTypes;
}