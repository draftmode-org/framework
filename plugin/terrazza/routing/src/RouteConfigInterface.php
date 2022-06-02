<?php
namespace Terrazza\Routing;

interface RouteConfigInterface {
    public function getPaths() : array;
    public function getPath(string $uri, string $method) :?array;
    public function getContentTypes(string $uri, string $method) :?array;
    public function getPathParameters(string $uri, string $method) : array;
}