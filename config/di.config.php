<?php

use Terrazza\Http\Routing\HttpRequestHandlerBuilderInterface;
use Terrazza\Http\Routing\HttpRouteLoaderInterface;
use Terrazza\Http\Routing\HttpRouter;
use Terrazza\Http\Routing\HttpRouterInterface;
use Terrazza\Http\Routing\HttpRouting;
use Terrazza\Http\Routing\HttpRoutingInterface;
use Terrazza\Http\Routing\OpenApi\OpenApiYaml;
use Terrazza\Http\Routing\OpenApi\RequestHandlerBuilder\OperationClassMethodRequestHandler;
use Terrazza\Injector\Injector;
use Terrazza\Injector\InjectorInterface;
use Terrazza\Routing\RouteMatcher;
use Terrazza\Routing\RouteMatcherInterface;
return [
    InjectorInterface::class                        => Injector::class,

    RouteMatcherInterface::class                    => RouteMatcher::class,
    HttpRouterInterface::class                      => HttpRouter::class,
    HttpRoutingInterface::class                     => HttpRouting::class,

    HttpRouteLoaderInterface::class                 => OpenApiYaml::class,
    OpenApiYaml::class                              => ["yamlFilename" => "../api.yaml"],
    HttpRequestHandlerBuilderInterface::class       => OperationClassMethodRequestHandler::class,
    OperationClassMethodRequestHandler::class=> ["controllerPath" => "Terrazza/Framework/Controller/Http/Http{ClassName}Controller"]
];