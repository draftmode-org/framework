<?php
namespace Terrazza\Http\Message\Uri;
use Psr\Http\Message\UriInterface;

interface UriHelperInterface {
    public static function isAbsolute(UriInterface $uri): bool;
    public static function isAbsolutePathReference(UriInterface $uri): bool;
    public static function isRelativePathReference(UriInterface $uri): bool;
    public static function isNetworkPathReference(UriInterface $uri): bool;
    public static function isDefaultPort(UriInterface $uri): bool;
}