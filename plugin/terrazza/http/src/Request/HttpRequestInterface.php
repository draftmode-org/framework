<?php
namespace Terrazza\Http\Request;
use Psr\Http\Message\RequestInterface;

interface HttpRequestInterface extends RequestInterface {
    /**
     * @return string|null
     */
    public function getContentType() :?string;
}