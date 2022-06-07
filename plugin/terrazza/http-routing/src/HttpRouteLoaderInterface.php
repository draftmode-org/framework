<?php
namespace Terrazza\Http\Routing;

interface HttpRouteLoaderInterface {
    /**
     * @return array
     */
    public function getPaths() : array;

    /**
     * @param string $uri
     * @param string $method
     * @return array|null
     */
    public function getPath(string $uri, string $method) :?array;

    /**
     * @param string $uri
     * @param string $method
     * @param string|null $contentType
     * @return string
     * @throw RuntimeException
     */
    public function getRequestHandlerClass(string $uri, string $method, ?string $contentType=null) : string;

    /**
     * @param string $uri
     * @param string $method
     * @return array|null
     */
    public function getContentTypes(string $uri, string $method) :?array;

    /**
     * @param string $uri
     * @param string $method
     * @return array
     */
    public function getPathParameters(string $uri, string $method) : array;
}