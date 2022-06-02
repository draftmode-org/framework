<?php
namespace Terrazza\Http\Request;
use Psr\Http\Message\UriInterface;

interface HttpRequestInterface {
    /**
     * @return UriInterface
     */
    public function getUri() : UriInterface;

    /**
     * @return string
     */
    public function getMethod() : string;

    /**
     * @return string|null
     */
    public function getContentType() :?string;
}