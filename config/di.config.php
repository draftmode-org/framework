<?php

use Psr\Container\ContainerInterface;
use Terrazza\Http\Routing\HttpRoutingInterface;
use Terrazza\Http\Routing\OpenApi\OpenApiRouting;
use Terrazza\Http\Routing\OpenApi\OpenApiYaml;
use Terrazza\Injector\Injector;
use Terrazza\Injector\InjectorInterface;
use Terrazza\Routing\RouteConfigInterface;
use Terrazza\Routing\RouteMatcher;
use Terrazza\Routing\RouteMatcherInterface;

return [
    ContainerInterface::class                       => Injector::class,

    RouteMatcherInterface::class                    => RouteMatcher::class,
    RouteConfigInterface::class                     => OpenApiYaml::class,
    HttpRoutingInterface::class                     => OpenApiRouting::class,
    OpenApiYaml::class                              => ["yamlFilename" => "../api.yaml"]
];