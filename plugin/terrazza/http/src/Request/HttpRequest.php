<?php
namespace Terrazza\Http\Request;

use Psr\Http\Message\UriInterface;

class HttpRequest implements HttpRequestInterface {
    private UriInterface $uri;
    private string $method;
    private array $headers;
    public function __construct(UriInterface $uri, string $method, array $headers=null) {
        $this->uri = $uri;
        $this->method = $method;
        $this->headers = $headers ?? [];
    }

    /**
     * @return UriInterface
     */
    public function getUri() : UriInterface {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getMethod() : string {
        return $this->method;
    }

    /**
     * @return string|null
     */
    public function getContentType() :?string {
        return $this->headers["Content-Type"] ?? null;
    }
}