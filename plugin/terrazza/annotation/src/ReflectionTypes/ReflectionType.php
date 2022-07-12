<?php
namespace Terrazza\Annotation\ReflectionTypes;

class ReflectionType {
    CONST builtIn                                       = ["array", "callable", "bool", "float", "int", "string", "iterable", "object", "mixed", "null"];
    private string $name;
    private bool $multiple;
    public function __construct(string $name, bool $multiple=false) {
        $this->name = $name;
        $this->multiple = $multiple;
    }

    public function __toString() {
        return $this->name;
    }

    public function getName() : string {
        return $this->name;
    }

    public function isBuiltin() : bool {
        return in_array($this->name, self::builtIn);
    }

    public function isMultiple() : bool {
        return $this->multiple;
    }

    public function withType(string $name) : self {
        $type                                       = clone $this;
        $type->name                                 = $name;
        return $type;
    }
}