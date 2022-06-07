<?php
namespace Terrazza\Http\Routing\Exception;
use InvalidArgumentException;
use Throwable;

class HttpUnsupportedContentType extends InvalidArgumentException {
    public function __construct(string $requestContentType) {
        parent::__construct("media-/content-type $requestContentType unsupported", 415);
    }
}