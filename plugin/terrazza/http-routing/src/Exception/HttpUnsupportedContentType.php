<?php
namespace Terrazza\Http\Routing\Exception;

class HttpUnsupportedContentType extends HttpRouteNotFoundException {
    public function __construct(string $requestContentType) {
        parent::__construct("media-/content-type $requestContentType unsupported", 415);
    }
}