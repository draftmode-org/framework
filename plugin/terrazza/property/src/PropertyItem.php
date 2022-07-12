<?php
namespace Terrazza\Property;

use Terrazza\Property\Exception\InvalidPropertyParamException;
use Terrazza\Property\Exception\InvalidPropertyTypeException;
use Terrazza\Property\Exception\InvalidPropertyValueException;

class PropertyItem {
    CONST allowed_types = ["number", "integer", "double", "array", "boolean", "string", "object", /*specials*/ "oneOf"];
    private string $name;
    private string $type;
    private bool $required=false;
    private bool $nullable=false;
    private ?string $patterns=null;
    private ?string $format=null;
    private ?int $minLength=null;
    private ?int $maxLength=null;
    private ?int $minItems=null;
    private ?int $maxItems=null;
    private ?float $minRange=null;
    private ?float $maxRange=null;
    private ?float $multipleOf=null;
    private ?array $enum=null;
    /**
     * @var self[]|null
     */
    public ?array $childSchemas=null;

    public function __construct (string $name, string $type) {
        $this->name                                 = $name;
        $this->validateType($type);
        $this->type                                 = $type;
    }

    public function getName() : string {
        return $this->name;
    }

    public function isRequired() : bool {
        return $this->required;
    }
    public function setRequired(bool $required) : self {
        $this->required = $required;
        return $this;
    }

    public function isNullable() : bool {
        return $this->nullable;
    }
    public function setNullable(bool $nullable) : self {
        $this->nullable = $nullable;
        return $this;
    }

    private function validateType(string $type) : void {
        if (!in_array($type, self::allowed_types)) {
            throw new InvalidPropertyTypeException("allowed types: ".join(",", self::allowed_types).", given $type");
        }
    }
    /**
     * @param string $type
     * @return $this
     * @throws InvalidPropertyTypeException
     */
    public function setType(string $type) : self {
        $this->validateType($type);
        $this->type                                 = $type;
        return $this;
    }
    public function getType() : string {
        return $this->type;
    }
    public function isMultipleType() : bool {
        return in_array($this->type, ["oneOf"]);
    }

    /**
     * @param self ...$childSchemas
     * @return $this
     */
    public function setChildSchemas(self ...$childSchemas) : self {
        $this->childSchemas                         = $childSchemas;
        return $this;
    }
    public function hasChildSchemas() : bool {
        return (is_array($this->childSchemas) && count($this->childSchemas));
    }
    /**
     * @return self[]|null
     */
    public function getChildSchemas() : ?array {
        return $this->childSchemas;
    }

    public function setPatterns(?string $patterns) : self {
        $this->patterns                             = $patterns;
        return $this;
    }
    public function getPatterns(): ?string {
        return $this->patterns;
    }

    public function setFormat(?string $format) : self {
        $this->format = $format;
        return $this;
    }
    public function getFormat(): ?string {
        return $this->format;
    }

    public function setMinLength(?int $minLength): self {
        if ($this->maxLength && $this->maxLength < $minLength) {
            throw new InvalidPropertyValueException("minLength $minLength cannot be greater than maxLength ".$this->maxLength);
        }
        $this->minLength = $minLength;
        return $this;
    }
    public function getMinLength(): ?int {
        return $this->minLength;
    }

    public function setMaxLength(?int $maxLength): self {
        if ($this->minLength && $this->minLength > $maxLength) {
            throw new InvalidPropertyValueException("maxLength $maxLength cannot be lower than minLength ".$this->minLength);
        }
        $this->maxLength = $maxLength;
        return $this;
    }
    public function getMaxLength(): ?int {
        return $this->maxLength;
    }

    public function setMinItems(?int $minItems): self {
        if ($this->maxItems && $this->maxItems < $minItems) {
            throw new InvalidPropertyValueException("minItems $minItems cannot be greater than maxLength ".$this->maxItems);
        }
        $this->minItems = $minItems;
        return $this;
    }
    public function getMinItems(): ?int {
        return $this->minItems;
    }

    public function setMaxItems(?int $maxItems): self {
        if ($this->minItems && $this->minItems > $maxItems) {
            throw new InvalidPropertyValueException("maxItems $maxItems cannot be lower than minItems ".$this->minItems);
        }
        $this->maxItems = $maxItems;
        return $this;
    }
    public function getMaxItems(): ?int {
        return $this->maxItems;
    }

    public function setMinRange(?float $minRange): self {
        if ($this->maxRange && $this->maxRange < $minRange) {
            throw new InvalidPropertyValueException("minRange $minRange cannot be greater than maxRange ".$this->maxRange);
        }
        $this->minRange = $minRange;
        return $this;
    }
    public function getMinRange(): ?float {
        return $this->minRange;
    }

    public function setMaxRange(?float $maxRange): self {
        if ($this->minRange && $this->minRange > $maxRange) {
            throw new InvalidPropertyValueException("maxRange $maxRange cannot be lower than minRange ".$this->minRange);
        }
        $this->maxRange = $maxRange;
        return $this;
    }
    public function getMaxRange(): ?float {
        return $this->maxRange;
    }

    public function setMultipleOf(?float $multipleOf): self {
        $this->multipleOf = $multipleOf;
        return $this;
    }
    public function getMultipleOf(): ?float {
        return $this->multipleOf;
    }
    public function setEnum(?array $enum=null) : self {
        $this->enum = $enum;
        return $this;
    }
    public function hasEnum() : bool {
        return (is_array($this->enum) && count($this->enum));
    }
    public function getEnum() : ?array {
        return $this->enum;
    }
}