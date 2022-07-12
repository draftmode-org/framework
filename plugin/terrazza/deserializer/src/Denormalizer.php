<?php
namespace Terrazza\Deserializer;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use Terrazza\Annotation\ReflectionTypesBuilderInterface;

class Denormalizer {
    private ReflectionTypesBuilderInterface $reflectionUnionTypeBuilder;
    public function __construct(ReflectionTypesBuilderInterface $reflectionUnionTypeBuilder) {
        $this->reflectionUnionTypeBuilder           = $reflectionUnionTypeBuilder;
    }

    /**
     * @param class-string<T> $className
     * @return T
     * @template T of object
     * @throws ReflectionException
     * @throws InvalidArgumentException
     */
    public function denormalizeClass(string $className) : void {
        $rClass                                     = new ReflectionClass($className);
        foreach ($rClass->getProperties() as $property) {
            echo "propertyName:".$property->getName().":<br><pre>";
            $types                                  = $this->reflectionUnionTypeBuilder->getTypes($property);
            foreach ($types->getTypes() as $type) {
                echo ":type:".$type->getName().":builtIn:".($type->isBuiltin() ? "yes": "no").":isMultiple:".($type->isMultiple() ? "yes" : "no")."<br>";
            }
            var_dump($types->isNullable());
            echo "</pre>";
        }
    }
}