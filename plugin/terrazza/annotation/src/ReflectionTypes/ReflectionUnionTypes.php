<?php

namespace Terrazza\Annotation\ReflectionTypes;

class ReflectionUnionTypes {
    private bool $nullable;
    /** @var ReflectionType[]  */
    private array $types;
    public function __construct(bool $nullable, ReflectionType ...$types) {
        $this->nullable = $nullable;
        $this->types = $types;
    }

    /** @return ReflectionType[] */
    public function getTypes(): array {
        return $this->types;
    }

    /**
     * @return bool
     */
    public function isNullable() : bool {
        return $this->nullable;
    }
}