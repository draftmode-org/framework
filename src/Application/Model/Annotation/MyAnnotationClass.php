<?php
namespace Terrazza\Framework\Application\Model\Annotation;
use Terrazza\Framework\Application\Model\Inline\MyInlineClass;

class MyAnnotationClass {
    /** @var MyInlineClass[]|null $oneOf */
    private $oneOf;
    /** @var float|int|null $my */
    private $multipleNullable;
    private ?int $intNullable;
    private int $intRequired;
}