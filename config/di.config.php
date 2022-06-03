<?php

use Terrazza\Http\Routing\HttpRouterInterface;
use Terrazza\Http\Routing\HttpRoutingInterface;
use Terrazza\Http\Routing\OpenApi\OpenApiRouter;
use Terrazza\Http\Routing\OpenApi\OpenApiRouting;
use Terrazza\Http\Routing\OpenApi\OpenApiYaml;
use Terrazza\Injector\Injector;
use Terrazza\Injector\InjectorInterface;
use Terrazza\Routing\RouteConfigInterface;
use Terrazza\Routing\RouteMatcher;
use Terrazza\Routing\RouteMatcherInterface;

return [
    InjectorInterface::class                       => Injector::class,

    RouteMatcherInterface::class                    => RouteMatcher::class,
    RouteConfigInterface::class                     => OpenApiYaml::class,
    HttpRouterInterface::class                      => OpenApiRouter::class,
    HttpRoutingInterface::class                     => OpenApiRouting::class,
    OpenApiYaml::class                              => ["yamlFilename" => "../api.yaml"]
];